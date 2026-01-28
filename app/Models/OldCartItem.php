<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OldCartItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'old_cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the old cart that owns the item.
     */
    public function oldCart(): BelongsTo
    {
        return $this->belongsTo(OldCart::class);
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
        return $this->hasMany(OldCartItem::class);
    }
}
