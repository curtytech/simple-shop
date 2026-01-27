<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the cart that owns the cart item.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product that belongs to the cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the subtotal for this cart item.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->price;
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
