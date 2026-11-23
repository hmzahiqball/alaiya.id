<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PoBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$salesOrders, $batches, $variantSummary, $filters] = $this->buildReportData($request);

        return view('admin.reports', [
            'salesOrders' => $salesOrders,
            'batches' => $batches,
            'variantSummary' => $variantSummary,
            'filters' => $filters,
            'totalSales' => $salesOrders->sum('subtotal'),
            'totalShipping' => $salesOrders->sum('shipping_cost'),
        ]);
    }

    public function exportSales(Request $request): StreamedResponse
    {
        [$salesOrders] = $this->buildReportData($request);

        return response()->streamDownload(function () use ($salesOrders) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No. PO', 'Pelanggan', 'Batch', 'Tanggal Verifikasi (WIB)', 'Subtotal Barang', 'Ongkir', 'Total']);
            foreach ($salesOrders as $o) {
                $approved = $o->payments->firstWhere('status', 'approved');
                fputcsv($out, [
                    $o->order_number,
                    $o->user->name ?? '-',
                    $o->batch->batch_code ?? '-',
                    $approved?->verified_at?->timezone('Asia/Jakarta')->format('Y-m-d H:i') ?? '-',
                    $o->subtotal,
                    $o->shipping_cost,
                    $o->total_amount,
                ]);
            }
            fclose($out);
        }, 'laporan-penjualan-alaiya-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv']);
    }

    public function exportQuota(Request $request): StreamedResponse
    {
        [, , $variantSummary] = $this->buildReportData($request);

        return response()->streamDownload(function () use ($variantSummary) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Produk', 'Varian', 'Unit Dibayar (Produksi)', 'Unit Dicadangkan']);
            foreach ($variantSummary as $row) {
                fputcsv($out, [$row['product'], $row['variant'], $row['paid_units'], $row['reserved_units']]);
            }
            fclose($out);
        }, 'rekap-kuota-produksi-alaiya-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function buildReportData(Request $request): array
    {
        $batchId = $request->integer('batch_id');
        $dateFrom = $request->string('date_from')->toString();
        $dateTo = $request->string('date_to')->toString();

        $paidStatuses = [Order::STATUS_PAID, Order::STATUS_IN_PRODUCTION, Order::STATUS_SHIPPED, Order::STATUS_COMPLETED];
        $reservedStatuses = [Order::STATUS_WAITING_PAYMENT, Order::STATUS_WAITING_VERIFICATION, Order::STATUS_NEEDS_REVISION];

        $salesOrders = Order::query()
            ->with(['user', 'batch', 'payments'])
            ->whereIn('status', $paidStatuses)
            ->when($batchId > 0, fn ($q) => $q->where('po_batch_id', $batchId))
            ->when($dateFrom !== '' || $dateTo !== '', function ($q) use ($dateFrom, $dateTo) {
                $q->whereHas('payments', function ($pq) use ($dateFrom, $dateTo) {
                    $pq->where('status', 'approved');
                    if ($dateFrom !== '') {
                        $pq->where('verified_at', '>=', Carbon::parse($dateFrom, 'Asia/Jakarta')->startOfDay()->utc());
                    }
                    if ($dateTo !== '') {
                        $pq->where('verified_at', '<=', Carbon::parse($dateTo, 'Asia/Jakarta')->endOfDay()->utc());
                    }
                });
            })
            ->orderByDesc('id')
            ->get();

        $batches = PoBatch::with('product')
            ->when($batchId > 0, fn ($q) => $q->whereKey($batchId))
            ->orderByDesc('id')
            ->get();

        $items = OrderItem::query()
            ->with(['order'])
            ->whereHas('order', function ($q) use ($batchId, $paidStatuses, $reservedStatuses) {
                $q->whereIn('status', array_merge($paidStatuses, $reservedStatuses));
                if ($batchId > 0) {
                    $q->where('po_batch_id', $batchId);
                }
            })
            ->get();

        $variantSummary = $items->groupBy(fn ($i) => $i->product_name_snapshot . '||' . $i->variant_name_snapshot)
            ->map(function ($group) use ($paidStatuses, $reservedStatuses) {
                $first = $group->first();
                return [
                    'product' => $first->product_name_snapshot,
                    'variant' => $first->variant_name_snapshot,
                    'paid_units' => $group->filter(fn ($i) => in_array($i->order->status, $paidStatuses, true))->sum('quantity'),
                    'reserved_units' => $group->filter(fn ($i) => in_array($i->order->status, $reservedStatuses, true))->sum('quantity'),
                ];
            })->values();

        return [$salesOrders, $batches, $variantSummary, compact('batchId', 'dateFrom', 'dateTo')];
    }
}
