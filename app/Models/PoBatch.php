<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoBatch extends Model
{
    protected $fillable = [
        'product_id', 'batch_code', 'title', 'start_at', 'end_at',
        'quota_total', 'allocated_units', 'status', 'production_estimate',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'quota_total' => 'integer',
            'allocated_units' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function remainingQuota(): int
    {
        return max(0, $this->quota_total - $this->allocated_units);
    }

    public function isAcceptingOrders(): bool
    {
        return $this->status === 'open'
            && $this->product?->is_active
            && now()->between($this->start_at, $this->end_at)
            && $this->remainingQuota() > 0;
    }
}
