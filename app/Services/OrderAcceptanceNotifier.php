<?php

namespace App\Services;

use App\Mail\OrderAcceptedMail;
use App\Models\CustomerNotification;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OrderAcceptanceNotifier
{
    public function notify(Order $order): void
    {
        $order->loadMissing(['client', 'store', 'product']);

        $notification = CustomerNotification::firstOrCreate(
            [
                'client_id' => $order->client_id,
                'order_id' => $order->id,
                'type' => 'order_accepted',
            ],
            [
                'title' => 'Encomenda aceita',
                'message' => sprintf(
                    'A loja %s aceitou sua encomenda de %s. Você já pode realizar o pagamento.',
                    $order->store->name,
                    $order->product->name,
                ),
                'action_url' => route('store.show', ['slug' => $order->store->slug]),
            ],
        );

        if ($notification->email_sent_at) {
            return;
        }

        try {
            Mail::to($order->client->email)->send(new OrderAcceptedMail($order));
            $notification->update(['email_sent_at' => now()]);
        } catch (Throwable $exception) {
            Log::error('Não foi possível enviar o e-mail de encomenda aceita.', [
                'order_id' => $order->id,
                'client_id' => $order->client_id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
