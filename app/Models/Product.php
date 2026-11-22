<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'category', 'price', 'material',
        'dimensions', 'description', 'image_url', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(PoBatch::class)->orderByDesc('id');
    }

    public function openBatch(): HasOne
    {
        return $this->hasOne(PoBatch::class)->where('status', 'open')->latestOfMany();
    }

    public function latestBatch(): HasOne
    {
        return $this->hasOne(PoBatch::class)->latestOfMany();
    }
}
