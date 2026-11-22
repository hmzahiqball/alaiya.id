<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_WAITING_PAYMENT = 'menunggu_pembayaran';
    public const STATUS_WAITING_VERIFICATION = 'menunggu_verifikasi';
    public const STATUS_NEEDS_REVISION = 'perlu_perbaikan';
    public const STATUS_PAID = 'dibayar';
    public const STATUS_IN_PRODUCTION = 'diproduksi';
    public const STATUS_SHIPPED = 'dikirim';
    public const STATUS_COMPLETED = 'selesai';
    public const STATUS_CANCELLED = 'dibatalkan';
    public const STATUS_EXPIRED = 'kedaluwarsa';

    public const STATUS_LABELS = [
        self::STATUS_WAITING_PAYMENT => 'Menunggu Pembayaran',
        self::STATUS_WAITING_VERIFICATION => 'Menunggu Verifikasi',
        self::STATUS_NEEDS_REVISION => 'Perlu Perbaikan',
        self::STATUS_PAID => 'Dibayar',
        self::STATUS_IN_PRODUCTION => 'Diproduksi',
        self::STATUS_SHIPPED => 'Dikirim',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Dibatalkan',
        self::STATUS_EXPIRED => 'Kedaluwarsa',
    ];

    protected $fillable = [
        'order_number', 'user_id', 'po_batch_id', 'recipient_name',
        'recipient_phone', 'shipping_address', 'postal_code', 'courier',
        'shipping_cost', 'subtotal', 'total_amount', 'payment_deadline',
        'status', 'tracking_number', 'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'payment_deadline' => 'datetime',
            'shipping_cost' => 'integer',
            'subtotal' => 'integer',
            'total_amount' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PoBatch::class, 'po_batch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderByDesc('id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('id');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function canUploadProof(): bool
    {
        return in_array($this->status, [self::STATUS_WAITING_PAYMENT, self::STATUS_NEEDS_REVISION], true)
            && now()->lte($this->payment_deadline);
    }

    public function totalQuantity(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
