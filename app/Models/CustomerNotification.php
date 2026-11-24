<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'order_id',
        'type',
        'title',
        'message',
        'action_url',
        'read_at',
        'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
