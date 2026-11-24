<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing(['client', 'store', 'product']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Sua encomenda foi aceita');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.orders.accepted');
    }
}
