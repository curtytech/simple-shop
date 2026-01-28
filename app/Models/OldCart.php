<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OldCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
    ];

    /**
     * Get the client that owns the oldCart.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the store (user) that owns the oldCart.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the oldCart items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OldCartItem::class);
    }

    /**
     * Get the total amount of the oldCart.
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get the total quantity of items in the oldCart.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }


    /**
     * Clear all items from the oldCart.
     */
    public function clear(): bool
    {
        return $this->items()->delete() > 0;
    }
}