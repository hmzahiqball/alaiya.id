<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\PoBatch;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public const FLAT_SHIPPING_COST = 12000;

    public function createFromCart(User $user, array $cart, array $shipping): Order
    {
        $batchId = $cart['batch_id'] ?? null;
        $items = $cart['items'] ?? [];

        if (!$batchId || empty($items)) {
            throw ValidationException::withMessages(['cart' => 'Keranjang PO Anda masih kosong.']);
        }

        return DB::transaction(function () use ($user, $batchId, $items, $shipping) {
            /** @var PoBatch $batch */
            $batch = PoBatch::query()
                ->with('product')
                ->whereKey($batchId)
                ->lockForUpdate()
                ->firstOrFail();

            if (!$batch->product || !$batch->product->is_active) {
                throw ValidationException::withMessages(['cart' => 'Produk sudah tidak aktif.']);
            }

            if ($batch->status !== 'open' || !now()->between($batch->start_at, $batch->end_at)) {
                throw ValidationException::withMessages(['cart' => 'Batch PO sudah ditutup atau di luar periode pemesanan.']);
            }

            $totalQty = collect($items)->sum('quantity');
            if ($totalQty <= 0) {
                throw ValidationException::withMessages(['cart' => 'Jumlah pesanan harus lebih dari 0.']);
            }

            if ($batch->remainingQuota() < $totalQty) {
                throw ValidationException::withMessages([
                    'cart' => 'Kuota batch tidak mencukupi. Sisa kuota saat ini: ' . $batch->remainingQuota() . ' pcs.',
                ]);
            }

            $subtotal = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $variant = ProductVariant::query()
                    ->whereKey($item['variant_id'])
                    ->where('product_id', $batch->product_id)
                    ->where('is_active', true)
                    ->first();

                if (!$variant) {
                    throw ValidationException::withMessages(['cart' => 'Salah satu varian sudah tidak aktif atau tidak sesuai batch.']);
                }

                $qty = (int) $item['quantity'];
                $unitPrice = (int) $batch->product->price;
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $preparedItems[] = [
                    'product_variant_id' => $variant->id,
                    'product_name_snapshot' => $batch->product->name,
                    'variant_name_snapshot' => $variant->color_name,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                ];
            }

            $batch->increment('allocated_units', $totalQty);

            $orderNumber = 'PO-' . now()->format('Y') . '-' . str_pad((string) (Order::max('id') + 1), 3, '0', STR_PAD_LEFT);
            $shippingCost = self::FLAT_SHIPPING_COST;

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'po_batch_id' => $batch->id,
                'recipient_name' => $shipping['recipient_name'],
                'recipient_phone' => $shipping['recipient_phone'],
                'shipping_address' => $shipping['shipping_address'],
                'postal_code' => $shipping['postal_code'],
                'courier' => $shipping['courier'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total_amount' => $subtotal + $shippingCost,
                'payment_deadline' => now()->addHours(24),
                'status' => Order::STATUS_WAITING_PAYMENT,
            ]);

            foreach ($preparedItems as $pItem) {
                $order->items()->create($pItem);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => Order::STATUS_WAITING_PAYMENT,
                'changed_by' => $user->id,
                'note' => "Pesanan dibuat. Kuota {$totalQty} pcs dicadangkan selama 24 jam.",
            ]);

            return $order;
        });
    }

    public function uploadProof(Order $order, User $actor, string $proofPath): Payment
    {
        return DB::transaction(function () use ($order, $actor, $proofPath) {
            $locked = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!$locked->canUploadProof()) {
                throw ValidationException::withMessages([
                    'proof' => 'Bukti pembayaran hanya dapat dikirim sebelum tenggat pada pesanan Menunggu Pembayaran atau Perlu Perbaikan.',
                ]);
            }

            $payment = $locked->payments()->create([
                'proof_path' => $proofPath,
                'amount' => $locked->total_amount,
                'status' => 'pending',
            ]);

            $prev = $locked->status;
            $locked->update(['status' => Order::STATUS_WAITING_VERIFICATION]);

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'from_status' => $prev,
                'to_status' => Order::STATUS_WAITING_VERIFICATION,
                'changed_by' => $actor->id,
                'note' => 'Pelanggan mengunggah bukti transfer untuk verifikasi admin.',
            ]);

            return $payment;
        });
    }

    public function approvePayment(Order $order, Payment $payment, User $admin): void
    {
        DB::transaction(function () use ($order, $payment, $admin) {
            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status !== Order::STATUS_WAITING_VERIFICATION) {
                throw ValidationException::withMessages(['status' => 'Pesanan tidak berada pada status Menunggu Verifikasi.']);
            }

            $payment->update([
                'status' => 'approved',
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => null,
            ]);

            $lockedOrder->update(['status' => Order::STATUS_PAID]);

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'from_status' => Order::STATUS_WAITING_VERIFICATION,
                'to_status' => Order::STATUS_PAID,
                'changed_by' => $admin->id,
                'note' => 'Pembayaran disetujui oleh Admin.',
            ]);
        });
    }

    public function rejectPayment(Order $order, Payment $payment, User $admin, string $reason): void
    {
        DB::transaction(function () use ($order, $payment, $admin, $reason) {
            $lockedOrder = Order::query()->with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status !== Order::STATUS_WAITING_VERIFICATION) {
                throw ValidationException::withMessages(['status' => 'Pesanan tidak berada pada status Menunggu Verifikasi.']);
            }

            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            if (now()->gt($lockedOrder->payment_deadline)) {
                $this->releaseQuotaAndTerminate($lockedOrder, Order::STATUS_EXPIRED, $admin->id, "Bukti ditolak setelah tenggat: {$reason}");
            } else {
                $lockedOrder->update(['status' => Order::STATUS_NEEDS_REVISION]);
                OrderStatusHistory::create([
                    'order_id' => $lockedOrder->id,
                    'from_status' => Order::STATUS_WAITING_VERIFICATION,
                    'to_status' => Order::STATUS_NEEDS_REVISION,
                    'changed_by' => $admin->id,
                    'note' => "Bukti ditolak: {$reason}. Silakan unggah ulang sebelum tenggat.",
                ]);
            }
        });
    }

    public function cancelOrder(Order $order, ?User $actor, string $reason, bool $expired = false): void
    {
        DB::transaction(function () use ($order, $actor, $reason, $expired) {
            $lockedOrder = Order::query()->with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (!in_array($lockedOrder->status, [Order::STATUS_WAITING_PAYMENT, Order::STATUS_NEEDS_REVISION], true)) {
                throw ValidationException::withMessages(['status' => 'Hanya pesanan Menunggu Pembayaran atau Perlu Perbaikan yang dapat dibatalkan.']);
            }

            $targetStatus = $expired ? Order::STATUS_EXPIRED : Order::STATUS_CANCELLED;
            $this->releaseQuotaAndTerminate($lockedOrder, $targetStatus, $actor?->id, $reason);
        });
    }

    public function advanceFulfillment(
        Order $order,
        User $admin,
        string $targetStatus,
        ?string $courier = null,
        ?string $trackingNumber = null
    ): void {
        DB::transaction(function () use ($order, $admin, $targetStatus, $courier, $trackingNumber) {
            $lockedOrder = Order::query()->with('batch')->whereKey($order->id)->lockForUpdate()->firstOrFail();

            $allowedTransitions = [
                Order::STATUS_PAID => Order::STATUS_IN_PRODUCTION,
                Order::STATUS_IN_PRODUCTION => Order::STATUS_SHIPPED,
                Order::STATUS_SHIPPED => Order::STATUS_COMPLETED,
            ];

            if (($allowedTransitions[$lockedOrder->status] ?? null) !== $targetStatus) {
                throw ValidationException::withMessages(['status' => 'Transisi status pemenuhan tidak valid.']);
            }

            if ($targetStatus === Order::STATUS_IN_PRODUCTION) {
                if ($lockedOrder->batch->status !== 'closed') {
                    throw ValidationException::withMessages(['status' => 'Produksi hanya dapat dimulai setelah batch PO ditutup.']);
                }

                $hasPendingInBatch = Order::query()
                    ->where('po_batch_id', $lockedOrder->po_batch_id)
                    ->whereIn('status', [Order::STATUS_WAITING_PAYMENT, Order::STATUS_WAITING_VERIFICATION, Order::STATUS_NEEDS_REVISION])
                    ->exists();

                if ($hasPendingInBatch) {
                    throw ValidationException::withMessages(['status' => 'Masih ada pesanan dengan pembayaran tertunda pada batch ini (AC-16).']);
                }
            }

            if ($targetStatus === Order::STATUS_SHIPPED && (blank($courier) || blank($trackingNumber))) {
                throw ValidationException::withMessages(['tracking_number' => 'Kurir dan nomor resi wajib diisi saat menandai Dikirim (AC-16).']);
            }

            $prev = $lockedOrder->status;
            $lockedOrder->update(array_filter([
                'status' => $targetStatus,
                'courier' => $courier ?: $lockedOrder->courier,
                'tracking_number' => $trackingNumber ?: $lockedOrder->tracking_number,
            ], fn ($v) => $v !== null));

            OrderStatusHistory::create([
                'order_id' => $lockedOrder->id,
                'from_status' => $prev,
                'to_status' => $targetStatus,
                'changed_by' => $admin->id,
                'note' => $targetStatus === Order::STATUS_SHIPPED
                    ? "Dikirim via {$courier} (Resi: {$trackingNumber})."
                    : 'Status diperbarui menjadi ' . (Order::STATUS_LABELS[$targetStatus] ?? $targetStatus) . '.',
            ]);
        });
    }

    private function releaseQuotaAndTerminate(Order $lockedOrder, string $targetStatus, ?int $actorId, string $reason): void
    {
        $batch = PoBatch::query()->whereKey($lockedOrder->po_batch_id)->lockForUpdate()->firstOrFail();
        $qty = $lockedOrder->totalQuantity();
        $batch->update(['allocated_units' => max(0, $batch->allocated_units - $qty)]);

        $prev = $lockedOrder->status;
        $lockedOrder->update([
            'status' => $targetStatus,
            'cancellation_reason' => $reason,
        ]);

        OrderStatusHistory::create([
            'order_id' => $lockedOrder->id,
            'from_status' => $prev,
            'to_status' => $targetStatus,
            'changed_by' => $actorId,
            'note' => $reason,
        ]);
    }
}
