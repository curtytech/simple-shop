<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sell extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'old_cart_id',
        'total',
        'status',
        'mercadopago_preference_id',
        'mercadopago_payment_id',
        'mercadopago_status',
        'data_pagamento',
        'mercadopago_response',
        'expiration_date',
    ];

    /**
     * Get the client that made the purchase.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * Get the items from the associated old cart.
     * This acts as a direct relationship for easier access in Filament.
     */
    public function items()
    {
        return $this->hasMany(OldCartItem::class, 'old_cart_id', 'old_cart_id');
    }


    /**
     * Get the store (user) where the purchase was made.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the cart associated with the sell.
     */
    public function oldCart(): BelongsTo
    {
        return $this->belongsTo(OldCart::class);
    }
}
