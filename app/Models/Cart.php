<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
    ];

    /**
     * Get the client that owns the cart.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the store (user) that owns the cart.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the cart items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the total amount of the cart.
     */
    public function getTotalAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });
    }

    /**
     * Get the total quantity of items in the cart.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Add a product to the cart.
     */
    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $cartItem = $this->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $quantity,
                'price' => $product->price, // Atualiza o preço
            ]);
        } else {
            $cartItem = $this->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $cartItem;
    }

    /**
     * Remove a product from the cart.
     */
    public function removeProduct(Product $product): bool
    {
        return $this->items()->where('product_id', $product->id)->delete() > 0;
    }

    /**
     * Update the quantity of a product in the cart.
     */
    public function updateProductQuantity(Product $product, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeProduct($product);
        }

        return $this->items()
            ->where('product_id', $product->id)
            ->update(['quantity' => $quantity]) > 0;
    }

    /**
     * Clear all items from the cart.
     */
    public function clear(): bool
    {
        return $this->items()->delete() > 0;
    }
}