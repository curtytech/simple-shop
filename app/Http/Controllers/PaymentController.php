<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class PaymentController extends Controller
{
    public function createMercadoPagoPreference(Request $request)
    {
        $validated = $request->validate([
            'store_id' => ['required','integer','exists:users,id'],
            'items' => ['array'], // opcional se derivar do carrinho
            'success_url' => ['nullable','url'],
            'failure_url' => ['nullable','url'],
            'pending_url' => ['nullable','url'],
        ]);

        $store = User::findOrFail($validated['store_id']);

        if (!$store->mp_access_token || !$store->mp_public_key) {
            return response()->json([
                'message' => 'Loja não configurou Mercado Pago.',
            ], 422);
        }

        $items = $validated['items'] ?? [];

        // Carregar itens do carrinho do cliente autenticado, se não enviados
        if (empty($items) && auth('client')->check()) {
            $client = auth('client')->user();
            $cart = $client->getCartForStore($store);
            $items = $cart->items()->with('product')->get()->map(function ($item) {
                return [
                    'id' => (string) $item->product_id,
                    'title' => $item->product->name,
                    'quantity' => (int) $item->quantity,
                    'currency_id' => 'BRL',
                    'unit_price' => (float) $item->product->price,
                ];
            })->toArray();
        }

        if (empty($items)) {
            return response()->json([
                'message' => 'Nenhum item encontrado para criar o pagamento.',
            ], 422);
        }

        $payer = [];
        if (auth('client')->check()) {
            $payerClient = auth('client')->user();
            $payer = [
                'name' => $payerClient->name,
                'email' => $payerClient->email,
            ];
        }

        $externalReference = 'store:' . $store->id . '|client:' . (auth('client')->id() ?? 'guest');
        $callbackBase = url('/payments/mercadopago/callback');
        $preference = [
            'items' => $items,
            'payer' => $payer,
            'back_urls' => [
                'success' => $validated['success_url'] ?? $callbackBase . '/success',
                'failure' => $validated['failure_url'] ?? $callbackBase . '/failure',
                'pending' => $validated['pending_url'] ?? $callbackBase . '/pending',
            ],
            'auto_return' => 'approved',
            'notification_url' => route('mercadopago.webhook', ['store' => $store->id]),
            'statement_descriptor' => $store->name,
            'external_reference' => $externalReference,
            'metadata' => [
                'store_id' => $store->id,
                'client_id' => auth('client')->id(),
            ],
        ];

        $baseUrl = 'https://api.mercadopago.com';
        $endpoint = '/checkout/preferences';
        $token = $store->mp_access_token;

        $headers = [];
        if ($store->mp_integrator_id) {
            $headers['X-Integrator-Id'] = $store->mp_integrator_id;
        }

        $response = Http::withToken($token)
            ->withHeaders($headers)
            ->post($baseUrl . $endpoint, $preference);

        if (!$response->successful()) {
            return response()->json([
                'message' => 'Erro ao criar preferência no Mercado Pago.',
                'error' => $response->json(),
            ], 500);
        }

        $data = $response->json();

        return response()->json([
            'init_point' => $data['init_point'] ?? null,
            'sandbox_init_point' => $data['sandbox_init_point'] ?? null,
            'public_key' => $store->mp_public_key,
            'sandbox' => (bool) $store->mp_sandbox,
        ]);
    }

    public function webhook(Request $request, User $store)
    {
        // TODO: validar / consultar pagamento e atualizar status do pedido
        return response()->json(['status' => 'ok']);
    }
}