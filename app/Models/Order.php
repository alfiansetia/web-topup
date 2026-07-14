<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_amount',
        'status',
        'payment_ref',
        'payment_method',
        'payment_url',
        'payment_channel',
        'payment_fee',
        'payment_gateway_status',
        'payment_gateway_response',
        'payment_proof',
        'notes',
        'paid_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'payment_gateway_response' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Generate order number otomatis
    public static function generateOrderNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ymd') . '-';
        $lastOrder = static::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -5);
            return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        }

        return $prefix . '00001';
    }

    // Scope: filter by status
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
