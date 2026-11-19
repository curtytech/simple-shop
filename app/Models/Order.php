<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'product_id',
        'quantity',
        'total_price',
        'approved',
        'status',
        'observation',
        'sell_id',
        'payment_status',
    ];

    protected $casts = [
        'approved' => 'boolean',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
