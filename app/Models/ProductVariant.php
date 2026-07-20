<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{

    protected $fillable = [
        'product_id',
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['effective_price', 'is_discounted'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class, 'variant_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    // Harga efektif (diskon kalau ada)
    public function getEffectivePriceAttribute(): float
    {
        return $this->is_discounted ? $this->discount_price : $this->price;
    }

    // Apakah sedang diskon?
    public function getIsDiscountedAttribute(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    // Recalculate stock_count dari items yang available
    public function recalculateStock(): void
    {
        $this->update([
            'stock_count' => $this->items()->where('status', 'available')->count(),
        ]);
    }
}
