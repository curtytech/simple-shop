<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Cart;
use App\Models\OldCart;
use App\Models\OldCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createMercadoPagoPreference(Request $request)
    {

        $validated = $request->validate([
            'store_id' => ['required', 'integer', 'exists:users,id'],
            'items' => ['array'], // opcional se derivar do carrinho
            'success_url' => ['nullable', 'url'],
            'failure_url' => ['nullable', 'url'],
            'pending_url' => ['nullable', 'url'],
        ]);

        $store = User::findOrFail($validated['store_id']);

        if (!$store->mercadopago_access_token || !$store->mercadopago_public_key) {
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

        // Garantir back_urls válidas (tratando strings vazias)
        $baseAppUrl = config('app.url') ?: url('/');
        $baseAppUrl = rtrim($baseAppUrl, '/');

        $defaultSuccess = $baseAppUrl . '/payments/mercadopago/callback/success';
        $defaultFailure = $baseAppUrl . '/payments/mercadopago/callback/failure';
        $defaultPending = $baseAppUrl . '/payments/mercadopago/callback/pending';

        $successUrl = !empty($validated['success_url'] ?? null) ? $validated['success_url'] : $defaultSuccess;
        $failureUrl = !empty($validated['failure_url'] ?? null) ? $validated['failure_url'] : $defaultFailure;
        $pendingUrl = !empty($validated['pending_url'] ?? null) ? $validated['pending_url'] : $defaultPending;

        // Validação extra para URLs finais (evita erro do MP)
        foreach (['success' => $successUrl, 'failure' => $failureUrl, 'pending' => $pendingUrl] as $key => $url) {
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'message' => "URL inválida para back_urls.{$key}: {$url}. Configure APP_URL com um domínio público (https) ou envie URLs válidas no payload.",
                ], 422);
            }
        }

        $externalReference = (string) $cart->id;
        // Limitar descriptor a 22 caracteres ASCII (requisito do MP)
        $descriptor = Str::of($store->name)->ascii()->substr(0, 22)->trim();

        $preference = [
            'items' => $items,
            'payer' => $payer,
            'back_urls' => [
                'success' => $successUrl,
                'failure' => $failureUrl,
                'pending' => $pendingUrl,
            ],
            'auto_return' => 'approved',
            'notification_url' => route('mercadopago.webhook', ['store' => $store->id]),
            'statement_descriptor' => $descriptor,
            'external_reference' => $externalReference,
            'metadata' => [
                'store_id' => $store->id,
                'client_id' => auth('client')->id(),
            ],
            'binary_mode' => true,
        ];

        $baseUrl = 'https://api.mercadopago.com';
        $endpoint = '/checkout/preferences';
        $token = $store->mercadopago_access_token;

        $headers = []; // sem integrator-id, não há coluna correspondente

        $response = Http::withToken($token)
            ->withHeaders($headers)
            ->post($baseUrl . $endpoint, $preference);

        if (!$response->successful()) {
            $body = $response->json();
            $detail = $body['message'] ?? ($body['error'] ?? null);
            return response()->json([
                'message' => $detail ? ('Erro ao criar preferência no Mercado Pago: ' . $detail) : 'Erro ao criar preferência no Mercado Pago.',
                'error' => $body,
                'status' => $response->status(),
            ], 500);
        }

        $data = $response->json();

        $oldCart = OldCart::create([
            'client_id' => auth('client')->id(),
            'user_id' => $store->id,
            'total' => $cart->total,
            'status' => 'pending',
            'mercadopago_preference_id' => $data['id'] ?? null,
        ]);

        foreach ($cart->items as $item) {
            OldCartItem::create([
                'old_cart_id' => $oldCart->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);
        }

        Sell::create([
            'client_id' => auth('client')->id(),
            'user_id' => $store->id,
            'old_cart_id' => $oldCart->id,
            'total' => $cart->total,
            'status' => 'pending',
            'mercadopago_preference_id' => $data['id'] ?? null,
        ]);

        return response()->json([
            'init_point' => $data['init_point'] ?? null,
            'sandbox_init_point' => $data['sandbox_init_point'] ?? null,
            'public_key' => $store->mercadopago_public_key,
            'sandbox' => (bool) $store->mercadopago_sandbox,
        ]);
    }

    public function webhook(Request $request, User $store)
    {
        // Consulta detalhe do pagamento com o token da loja
        $accessToken = $store->mercadopago_access_token;
        if (!$accessToken) {
            Log::warning('Webhook recebido mas loja sem token', ['store_id' => $store->id]);
            return response()->json(['ok' => true], 200);
        }

        $paymentId = $request->input('data.id')
            ?? $request->input('id')
            ?? $request->query('id');

        if (!$paymentId) {
            Log::warning('Webhook sem paymentId', ['store_id' => $store->id, 'payload' => $request->all()]);
            return response()->json(['ok' => true], 200);
        }

        $resp = Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

        if (!$resp->successful()) {
            Log::error('Falha ao consultar pagamento do MP', [
                'store_id' => $store->id,
                'payment_id' => $paymentId,
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);
            return response()->json(['ok' => true], 200);
        }

        $detail = $resp->json();
        Log::info('Pagamento consultado com sucesso', [
            'store_id' => $store->id,
            'payment_id' => $paymentId,
            'status' => data_get($detail, 'status'),
            'amount' => data_get($detail, 'transaction_details.total_paid_amount'),
            'external_reference' => data_get($detail, 'external_reference'),
        ]);

        // Aqui você pode atualizar pedido/carrinho dessa store
        return response()->json(['ok' => true], 200);
    }

    public function callbackSuccess(Request $request)
    {
        Log::info('Mercado Pago Callback Success:', $request->all());

        // Tenta finalizar o pedido se o status for aprovado
        if ($request->collection_status === 'approved' && $request->external_reference) {
            $this->finalizeOrder($request->external_reference, $request->payment_id, 'approved');
        } else {
            Log::warning('Mercado Pago Callback: status não aprovado ou sem referência', $request->all());
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
        $redirectUrl = '/';

        if ($request->external_reference) {
            // A external_reference agora é o ID do carrinho
            $cart = Cart::find($request->external_reference);
            // Se encontrou o carrinho e a loja associada, redireciona para a loja
            if ($cart && $cart->store) {
                $redirectUrl = route('store.show', ['slug' => $cart->store->slug]);
            }
        }

        return redirect($redirectUrl)->with('status', 'Status do pagamento: ' . $status);
    }

    /**
     * Finaliza o pedido criando a venda e "removendo" o carrinho
     */
    private function finalizeOrder($cartId, $paymentId, $status = 'pending')
    {
        // $cartId aqui é a external_reference, que agora passamos como o ID do carrinho ATUAL (Cart)
        // Mas a Venda (Sell) está vinculada ao OldCart.
        // Precisamos encontrar a Venda pelo paymentId (mercadopago_preference_id)
        
        $sell = Sell::where('mercadopago_preference_id', $paymentId)->first();

        if ($sell) {
             if ($sell->status !== 'approved' && $status === 'approved') {
                $sell->update(['status' => 'approved']);
            }
        } else {
             Log::warning("Venda não encontrada para pagamento {$paymentId}.");
             // Fallback: tentar achar pelo carrinho se a lógica mudou? 
             // Como criamos a venda antes, ela DEVE existir.
        }
        
        // Limpa o carrinho atual
        $cart = Cart::find($cartId);
        if ($cart) {
             $cart->items()->delete();
        }
    }
}
