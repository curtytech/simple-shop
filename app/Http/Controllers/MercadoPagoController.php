<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Client;
use App\Models\Payment;
use App\Models\User;
use App\Models\Sell;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class MercadoPagoController extends Controller
{
    public function checkout(Request $request)
    {
        // $user = $request->user();

        $clientId = $request->input('client_id');
        if (!$clientId) {
            abort(400, 'ID do cliente é necessário.');
        }

        $storeId = $request->input('store_id');
        if (!$storeId) {
            abort(400, 'ID da loja é necessário.');
        }
        $user = User::where('id', $storeId)->firstOrFail();

        $cart = Cart::where('client_id', $clientId)
            ->where('user_id', $storeId)      // garante carrinho da loja certa
            ->with('items.product')           // carrega itens + produto
            ->firstOrFail();

        $client = Client::where('client_id', $clientId)
            ->firstOrFail();

        $cartItems = $cart->items;

        $items = $cartItems->map(function ($item) {
            return [
                'title' => $item->product->name,
                'quantity' => (int) $item->quantity,
                'currency_id' => 'BRL',
                'unit_price' => (float) $item->price,
            ];
        })->toArray();

        $publicKey = $user->mercadopago_public_key;
        if (!$publicKey) {
            abort(403, 'Chave pública do Mercado Pago não configurada.');
        }

        $accessToken = $user->mercadopago_access_token;
        if (!$accessToken) {
            abort(403, 'Token de acesso do Mercado Pago não configurado.');
        }

        $webhookUrl = $user->mercadopago_webhook_url;
        if (!$webhookUrl) {
            abort(403, 'URL de webhook do Mercado Pago não configurada.');
        }
        $webhookSecret = $user->mercadopago_webhook_secret;
        if (!$webhookSecret) {
            abort(403, 'Segredo de webhook do Mercado Pago não configurado.');
        }

        $sandbox = filter_var($user->mercadopago_sandbox, FILTER_VALIDATE_BOOLEAN);

        // Forçar URLs públicas em https
        $ensureHttps = function (string $url): string {
            if (str_starts_with($url, 'http://')) {
                return 'https://' . substr($url, 7);
            }
            return $url;
        };



        //  // Use Laravel's url() helper to generate absolute URLs
        // $successUrl = url('/payments/mercadopago/callback/success');
        // $failureUrl = url('/payments/mercadopago/callback/failure');
        // $pendingUrl = url('/payments/mercadopago/callback/pending');
        // Use Laravel's route() helper to generate absolute URLs
        $successUrl = route('mercadopago.callback.success');
        $failureUrl = route('mercadopago.callback.failure');
        $pendingUrl = route('mercadopago.callback.pending');

        // Ensure HTTPS for these URLs
        $successUrl = $ensureHttps($successUrl);
        $failureUrl = $ensureHttps($failureUrl);
        $pendingUrl = $ensureHttps($pendingUrl);

        if ($webhookUrl) {
            $webhookUrl = $ensureHttps($webhookUrl);
        }

        if (empty($items)) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'O carrinho está vazio.'], 400);
            }
            return back()->with('error', 'O carrinho está vazio.');
        }

        $payload = [
            'items' => $items,
            'payer' => [
                'name' => $client->name,
                'email' => $client->email,
            ],
            'back_urls' => [
                'success' => $successUrl,
                'failure' => $failureUrl,
                'pending' => $pendingUrl,
            ],
            'auto_return' => 'approved',
            'notification_url' => $webhookUrl,
            'external_reference' => (string) $cart->id, // Referência ao carrinho ou pedido
            'metadata' => [
                'store_id' => $user->id,
                'client_id' => $clientId,
                'cart_id' => $cart->id,
            ],
        ];

        Log::info('Payload Mercado Pago:', $payload);

        $response = Http::withToken($accessToken)->post('https://api.mercadopago.com/checkout/preferences', $payload);

        if (!$response->successful()) {
            Log::error('Erro ao criar preferência Mercado Pago', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Erro na API do Mercado Pago',
                    'detail' => $response->json()
                ], 500);
            }
            return back()->with('error', 'Erro na API do Mercado Pago: ' . $response->status());
        }

        $pref = $response->json();
        $redirectUrl = $sandbox ? ($pref['sandbox_init_point'] ?? $pref['init_point'] ?? null) : ($pref['init_point'] ?? null);
        
        if (!$redirectUrl) {
            if ($request->wantsJson()) {
                 return response()->json(['error' => 'URL de checkout não gerada'], 500);
            }
            return back()->with('error', 'Preferência criada, mas não há URL de checkout.');
        }

        if ($request->wantsJson()) {
            return response()->json([
                'init_point' => $pref['init_point'],
                'sandbox_init_point' => $pref['sandbox_init_point'],
                'sandbox' => $sandbox,
                'redirect_url' => $redirectUrl
            ]);
        }

        // Não criamos a venda aqui, apenas redirecionamos.
        // A venda será criada no callback ou webhook após confirmação do pagamento.

        return redirect()->away($redirectUrl);
    }

    public function callbackSuccess(Request $request)
    {
        // Tenta finalizar o pedido se o status for aprovado
        if ($request->collection_status === 'approved' && $request->external_reference) {
            $this->finalizeOrder($request->external_reference, $request->payment_id, 'approved');
        }
        
        return $this->handleCallback($request, 'success');
    }

    public function callbackFailure(Request $request)
    {
        return $this->handleCallback($request, 'failure');
    }

    public function callbackPending(Request $request)
    {
        if ($request->collection_status === 'pending' && $request->external_reference) {
            $this->finalizeOrder($request->external_reference, $request->payment_id, 'pending');
        }

        return $this->handleCallback($request, 'pending');
    }

    private function handleCallback(Request $request, string $status)
    {
        // Redireciona para a home com mensagem
        return redirect('/')->with('status', 'Status do pagamento: ' . $status);
    }

    /**
     * Finaliza o pedido criando a venda e "removendo" o carrinho
     */
    private function finalizeOrder($cartId, $paymentId, $status = 'pending')
    {
        $cart = Cart::find($cartId);

        if (!$cart) {
            Log::warning("Carrinho {$cartId} não encontrado ao finalizar pedido {$paymentId}.");
            return;
        }

        // Verifica se já existe venda para este carrinho (evita duplicidade)
        $existingSell = Sell::where('cart_id', $cart->id)->first();
        if ($existingSell) {
            // Se já existe, apenas atualiza status se necessário
            if ($existingSell->status !== 'approved' && $status === 'approved') {
                $existingSell->update(['status' => 'approved', 'mercadopago_preference_id' => $paymentId]);
            }
            return;
        }

        try {
            Sell::create([
                'client_id' => $cart->client_id,
                'user_id' => $cart->user_id,
                'cart_id' => $cart->id,
                'total' => $cart->total,
                'status' => $status === 'approved' ? 'approved' : 'pending',
                'mercadopago_preference_id' => $paymentId, // Usando o ID do pagamento aqui
            ]);

            // Soft delete no carrinho para manter histórico mas limpar da sessão atual
            $cart->delete();

        } catch (\Exception $e) {
            Log::error("Erro ao criar venda para carrinho {$cartId}: " . $e->getMessage());
        }
    }

    // public function webhook(Request $request)
    // {
    //     // Log básico do evento recebido
    //     Log::info('Webhook Mercado Pago recebido', [
    //         'query' => $request->query(),
    //         'body' => $request->all(),
    //         'headers' => [
    //             'Content-Type' => $request->header('Content-Type'),
    //             'X-Request-Id' => $request->header('X-Request-Id'),
    //         ],
    //     ]);

    //     $accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
    //     if (!$accessToken) {
    //         Log::error('MERCADOPAGO_ACCESS_TOKEN não configurado');
    //         return response()->json(['ok' => true], 200);
    //     }

    //     // Suporta payloads: { data: { id }, type: 'payment', action: 'payment.created' }
    //     // e também id via query string (?id=...&type=payment)
    //     $paymentId = $request->input('data.id')
    //         ?? $request->input('id')
    //         ?? $request->query('id');

    //     $eventType = $request->input('type') ?? $request->query('type') ?? 'payment';
    //     $action = $request->input('action') ?? null;

    //     if (!$paymentId) {
    //         Log::warning('Webhook sem paymentId', ['payload' => $request->all()]);
    //         return response()->json(['ok' => true], 200);
    //     }

    //     if ($eventType !== 'payment') {
    //         Log::info('Evento ignorado (não é payment)', ['type' => $eventType, 'action' => $action]);
    //         return response()->json(['ok' => true], 200);
    //     }

    //     // Consulta detalhes do pagamento
    //     $paymentResp = Http::withToken($accessToken)
    //         ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

    //     if (!$paymentResp->successful()) {
    //         Log::error('Falha ao consultar pagamento no Mercado Pago', [
    //             'payment_id' => $paymentId,
    //             'status' => $paymentResp->status(),
    //             'body' => $paymentResp->body(),
    //         ]);
    //         // Retorna 200 para evitar retries excessivos; mantemos logs para análise
    //         return response()->json(['ok' => true], 200);
    //     }

    //     $detail = $paymentResp->json();
    //     Log::info('Detalhe do pagamento obtido', [
    //         'payment_id' => $paymentId,
    //         'status' => data_get($detail, 'status'),
    //         'external_reference' => data_get($detail, 'external_reference'),
    //     ]);

    //     $status = data_get($detail, 'status');
    //     $dateApproved = data_get($detail, 'date_approved'); // ISO8601
    //     $userId = (int) (data_get($detail, 'metadata.user_id') ?? data_get($detail, 'external_reference') ?? 0);

    //     $dataPagamento = $dateApproved ? Carbon::parse($dateApproved) : null;
    //     $expiration = $dataPagamento ? $dataPagamento->copy()->addYear() : null;

    //     // Tentativas para preference_id
    //     $preferenceId =
    //         data_get($detail, 'metadata.preference_id')
    //         ?? data_get($detail, 'order.id')
    //         ?? null;

    //     Payment::updateOrCreate(
    //         ['mercadopago_payment_id' => (string) $paymentId],
    //         [
    //             'user_id' => $userId ?: null,
    //             'mercadopago_preference_id' => $preferenceId,
    //             'mercadopago_status' => $status,
    //             'data_pagamento' => $dataPagamento,
    //             'expiration_date' => $expiration,
    //             'mercadopago_response' => json_encode($detail),
    //         ]
    //     );

    //     Log::info('Pagamento registrado/atualizado com sucesso', [
    //         'payment_id' => $paymentId,
    //         'user_id' => $userId ?: null,
    //         'status' => $status,
    //     ]);

    //     return response()->json(['ok' => true], 200);
    // }
}
