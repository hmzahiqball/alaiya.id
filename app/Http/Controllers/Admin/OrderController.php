<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PoBatch;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->string('status')->toString();
        $batchFilter = $request->integer('batch_id');

        $orders = Order::query()
            ->with(['user', 'batch.product', 'items', 'payments'])
            ->when($statusFilter !== '', fn ($q) => $q->where('status', $statusFilter))
            ->when($batchFilter > 0, fn ($q) => $q->where('po_batch_id', $batchFilter))
            ->orderByDesc('id')
            ->get();

        $batches = PoBatch::with('product')->orderByDesc('id')->get();
        $selectedOrder = $request->integer('order_id')
            ? $orders->firstWhere('id', $request->integer('order_id'))
            : $orders->first();

        return view('admin.orders', compact('orders', 'batches', 'statusFilter', 'batchFilter', 'selectedOrder'));
    }

    public function approve(Request $request, Order $order, Payment $payment, OrderService $orderService): RedirectResponse
    {
        $orderService->approvePayment($order, $payment, $request->user());

        return redirect()->route('admin.orders.index', ['order_id' => $order->id])
            ->with('success', "Pembayaran {$order->order_number} disetujui (Status: Dibayar).");
    }

    public function reject(Request $request, Order $order, Payment $payment, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);
        $orderService->rejectPayment($order, $payment, $request->user(), $data['reason']);

        return redirect()->route('admin.orders.index', ['order_id' => $order->id])
            ->with('success', "Bukti pembayaran {$order->order_number} ditolak.");
    }

    public function advance(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate([
            'target_status' => ['required', 'in:diproduksi,dikirim,selesai'],
            'courier' => ['nullable', 'string', 'max:60'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $orderService->advanceFulfillment(
            $order,
            $request->user(),
            $data['target_status'],
            $data['courier'] ?? null,
            $data['tracking_number'] ?? null
        );

        return redirect()->route('admin.orders.index', ['order_id' => $order->id])
            ->with('success', "Status {$order->order_number} diperbarui.");
    }

    public function cancel(Request $request, Order $order, OrderService $orderService): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:300']]);
        $orderService->cancelOrder($order, $request->user(), $data['reason']);

        return redirect()->route('admin.orders.index', ['order_id' => $order->id])
            ->with('success', "Pesanan {$order->order_number} dibatalkan dan kuota dilepas.");
    }
}
