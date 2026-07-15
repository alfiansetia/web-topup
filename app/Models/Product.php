<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'instruction_use',
        'features',
        'checkout_instruction',
        'image',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    protected $appends = ['image_url', 'min_price'];

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->image
                    ? asset('storage/product/' . $this->image)
                    : asset('images/placeholder-product.svg');
            },
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Total stok dari semua variant
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock_count');
    }

    // Harga terendah dari semua variant
    public function getMinPriceAttribute(): ?float
    {
        return $this->variants->where('is_active', true)->min('price');
    }

    public static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = str($product->name)->slug();
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = str($product->name)->slug();
            }
        });
    }
}
