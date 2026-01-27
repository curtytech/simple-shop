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
    // public function checkout(Request $request)
    // {

    //     Sell::create([
    //         'client_id' => 1,
    //         'user_id' => 2,
    //         'cart_id' => 1,
    //         'total' => 99,
    //         'status' => 'pending',
    //         'mercadopago_preference_id' => '$paymentId', // Usando o ID do pagamento aqui
    //     ]);

    //     $clientId = $request->input('client_id');
    //     if (!$clientId) {
    //         abort(400, 'ID do cliente é necessário.');
    //     }

    //     $storeId = $request->input('store_id');
    //     if (!$storeId) {
    //         abort(400, 'ID da loja é necessário.');
    //     }
    //     $user = User::where('id', $storeId)->firstOrFail();

    //     $cart = Cart::where('client_id', $clientId)
    //         ->where('user_id', $storeId)      // garante carrinho da loja certa
    //         ->with('items.product')           // carrega itens + produto
    //         ->firstOrFail();

    //     $client = Client::where('client_id', $clientId)
    //         ->firstOrFail();

    //     $cartItems = $cart->items;

    //     $items = $cartItems->map(function ($item) {
    //         return [
    //             'title' => $item->product->name,
    //             'quantity' => (int) $item->quantity,
    //             'currency_id' => 'BRL',
    //             'unit_price' => (float) $item->price,
    //         ];
    //     })->toArray();

    //     $publicKey = $user->mercadopago_public_key;
    //     if (!$publicKey) {
    //         abort(403, 'Chave pública do Mercado Pago não configurada.');
    //     }

    //     $accessToken = $user->mercadopago_access_token;
    //     if (!$accessToken) {
    //         abort(403, 'Token de acesso do Mercado Pago não configurado.');
    //     }

    //     $webhookUrl = $user->mercadopago_webhook_url;
    //     if (!$webhookUrl) {
    //         abort(403, 'URL de webhook do Mercado Pago não configurada.');
    //     }
    //     $webhookSecret = $user->mercadopago_webhook_secret;
    //     if (!$webhookSecret) {
    //         abort(403, 'Segredo de webhook do Mercado Pago não configurado.');
    //     }




    //     $sandbox = filter_var($user->mercadopago_sandbox, FILTER_VALIDATE_BOOLEAN);

    //     // Forçar URLs públicas em https
    //     $ensureHttps = function (string $url): string {
    //         if (str_starts_with($url, 'http://')) {
    //             return 'https://' . substr($url, 7);
    //         }
    //         return $url;
    //     };

    //     // Use Laravel's route() helper to generate absolute URLs
    //     $successUrl = route('mercadopago.callback.success');
    //     $failureUrl = route('mercadopago.callback.failure');
    //     $pendingUrl = route('mercadopago.callback.pending');

    //     Log::info('URLs geradas antes do HTTPS:', [
    //         'success' => $successUrl,
    //         'failure' => $failureUrl,
    //         'pending' => $pendingUrl
    //     ]);

    //     // Ensure HTTPS for these URLs
    //     $successUrl = $ensureHttps($successUrl);
    //     $failureUrl = $ensureHttps($failureUrl);
    //     $pendingUrl = $ensureHttps($pendingUrl);

    //     if ($webhookUrl) {
    //         $webhookUrl = $ensureHttps($webhookUrl);
    //     }

    //     if (empty($items)) {
    //         if ($request->wantsJson()) {
    //             return response()->json(['error' => 'O carrinho está vazio.'], 400);
    //         }
    //         return back()->with('error', 'O carrinho está vazio.');
    //     }

    //     $payload = [
    //         'items' => $items,
    //         'payer' => [
    //             'name' => $client->name,
    //             'email' => $client->email,
    //         ],
    //         'back_urls' => [
    //             'success' => $successUrl,
    //             'failure' => $failureUrl,
    //             'pending' => $pendingUrl,
    //         ],
    //         'auto_return' => 'approved',
    //         'notification_url' => $webhookUrl,
    //         'external_reference' => (string) $cart->id, // Referência ao carrinho ou pedido
    //         'metadata' => [
    //             'store_id' => $user->id,
    //             'client_id' => $clientId,
    //             'cart_id' => $cart->id,
    //         ],
    //     ];

    //     Log::info('Payload Mercado Pago:', $payload);

    //     $response = Http::withToken($accessToken)->post('https://api.mercadopago.com/checkout/preferences', $payload);

    //     if (!$response->successful()) {
    //         Log::error('Erro ao criar preferência Mercado Pago', [
    //             'status' => $response->status(),
    //             'body' => $response->body(),
    //             'payload' => $payload,
    //         ]);

    //         if ($request->wantsJson()) {
    //             return response()->json([
    //                 'error' => 'Erro na API do Mercado Pago',
    //                 'detail' => $response->json()
    //             ], 500);
    //         }
    //         return back()->with('error', 'Erro na API do Mercado Pago: ' . $response->status());
    //     }

    //     $pref = $response->json();
    //     $redirectUrl = $sandbox ? ($pref['sandbox_init_point'] ?? $pref['init_point'] ?? null) : ($pref['init_point'] ?? null);

    //     if (!$redirectUrl) {
    //         if ($request->wantsJson()) {
    //             return response()->json(['error' => 'URL de checkout não gerada'], 500);
    //         }
    //         return back()->with('error', 'Preferência criada, mas não há URL de checkout.');
    //     }

    //     if ($request->wantsJson()) {
    //         return response()->json([
    //             'init_point' => $pref['init_point'],
    //             'sandbox_init_point' => $pref['sandbox_init_point'],
    //             'sandbox' => $sandbox,
    //             'redirect_url' => $redirectUrl
    //         ]);
    //     }

    //     // Não criamos a venda aqui, apenas redirecionamos.
    //     // A venda será criada no callback ou webhook após confirmação do pagamento.



    //     return redirect()->away($redirectUrl);
    // }

    // public function callbackSuccess(Request $request)
    // {
    //     Log::info('Mercado Pago Callback Success:', $request->all());

    //     // Tenta finalizar o pedido se o status for aprovado
    //     if ($request->collection_status === 'approved' && $request->external_reference) {
    //         $this->finalizeOrder($request->external_reference, $request->payment_id, 'approved');
    //     } else {
    //         Log::warning('Mercado Pago Callback: status não aprovado ou sem referência', $request->all());
    //     }

    //     return $this->handleCallback($request, 'success');
    // }

    // public function callbackFailure(Request $request)
    // {
    //     return $this->handleCallback($request, 'failure');
    // }

    // public function callbackPending(Request $request)
    // {
    //     if ($request->collection_status === 'pending' && $request->external_reference) {
    //         $this->finalizeOrder($request->external_reference, $request->payment_id, 'pending');
    //     }

    //     return $this->handleCallback($request, 'pending');
    // }

    // private function handleCallback(Request $request, string $status)
    // {
    //     // Redireciona para a home com mensagem
    //     return redirect('/')->with('status', 'Status do pagamento: ' . $status);
    // }

    // /**
    //  * Finaliza o pedido criando a venda e "removendo" o carrinho
    //  */
    // private function finalizeOrder($cartId, $paymentId, $status = 'pending')
    // {
    //     // echo 'Finalizando pedido para carrinho: ' . $cartId . ' com status: ' . $status . ' e pagamento: ' . $paymentId . '<br>';
    //     $cart = Cart::find($cartId);

    //     if (!$cart) {
    //         Log::warning("Carrinho {$cartId} não encontrado ao finalizar pedido {$paymentId}.");
    //         return;
    //     }

    //     // Verifica se já existe venda para este carrinho (evita duplicidade)
    //     $existingSell = Sell::where('cart_id', $cart->id)->first();
    //     if ($existingSell) {
    //         // Se já existe, apenas atualiza status se necessário
    //         if ($existingSell->status !== 'approved' && $status === 'approved') {
    //             $existingSell->update(['status' => 'approved', 'mercadopago_preference_id' => $paymentId]);
    //         }
    //         return;
    //     }

    //     try {
    //         Sell::create([
    //             'client_id' => $cart->client_id,
    //             'user_id' => $cart->user_id,
    //             'cart_id' => $cart->id,
    //             'total' => $cart->total,
    //             'status' => $status === 'approved' ? 'approved' : 'pending',
    //             'mercadopago_preference_id' => $paymentId, // Usando o ID do pagamento aqui
    //         ]);

    //         // Limpa os itens do carrinho (soft delete) para manter histórico mas esvaziar para nova compra
    //         $cart->items()->delete();
    //     } catch (\Exception $e) {
    //         Log::error("Erro ao criar venda para carrinho {$cartId}: " . $e->getMessage());
    //     }
    // }
}
