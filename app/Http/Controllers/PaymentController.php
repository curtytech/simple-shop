<?php

namespace App\Http\Controllers;

use App\Models\Sell;
use App\Models\Cart;
use App\Models\OldCart;
use App\Models\OldCartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createOrderPaymentPreference(Request $request, Order $order)
    {
        $client = auth('client')->user();
        abort_unless($order->client_id === $client?->id, 404);

        if (!$order->approved || $order->status === 'rejeitado') {
            return response()->json([
                'message' => 'A loja ainda não aprovou esta encomenda.',
            ], 422);
        }

        if ($order->payment_status === 'approved') {
            return response()->json(['message' => 'Esta encomenda já foi paga.'], 422);
        }

        $order->load(['store', 'product']);
        $store = $order->store;
        $credentials = $store->mercadopagoCredentials();

        if (!$credentials['access_token'] || !$credentials['public_key']) {
            return response()->json(['message' => 'A loja não configurou o Mercado Pago.'], 422);
        }

        $sell = $order->sell_id ? Sell::find($order->sell_id) : null;

        if (!$sell) {
            $oldCart = OldCart::create([
                'client_id' => $client->id,
                'user_id' => $store->id,
            ]);

            OldCartItem::create([
                'old_cart_id' => $oldCart->id,
                'product_id' => $order->product_id,
                'quantity' => $order->quantity,
                'price' => $order->total_price / max($order->quantity, 1),
                'type_of_sell' => 'encomenda',
            ]);

            $sell = Sell::create([
                'client_id' => $client->id,
                'user_id' => $store->id,
                'old_cart_id' => $oldCart->id,
                'total' => $order->total_price,
                'status' => 'pending',
            ]);

            $order->update(['sell_id' => $sell->id]);
        }

        $baseAppUrl = rtrim(config('app.url') ?: url('/'), '/');
        $preference = [
            'items' => [[
                'id' => (string) $order->product_id,
                'title' => $order->product->name . ' - Encomenda aprovada',
                'quantity' => (int) $order->quantity,
                'currency_id' => 'BRL',
                'unit_price' => round((float) $order->total_price / max($order->quantity, 1), 2),
            ]],
            'payer' => ['name' => $client->name, 'email' => $client->email],
            'back_urls' => [
                'success' => $baseAppUrl . '/payments/mercadopago/callback/success',
                'failure' => $baseAppUrl . '/payments/mercadopago/callback/failure',
                'pending' => $baseAppUrl . '/payments/mercadopago/callback/pending',
            ],
            'auto_return' => 'approved',
            'notification_url' => $credentials['webhook_url']
                ?: route('mercadopago.webhook', [
                    'store' => $store->id,
                    'environment' => $store->mercadopago_environment ?? 'production',
                ]),
            'statement_descriptor' => Str::of($store->name)->ascii()->substr(0, 22)->trim(),
            'external_reference' => (string) $sell->id,
            'metadata' => [
                'store_id' => $store->id,
                'client_id' => $client->id,
                'sell_id' => $sell->id,
                'order_id' => $order->id,
                'old_cart_id' => $sell->old_cart_id,
            ],
            'binary_mode' => true,
        ];

        $response = Http::withToken($credentials['access_token'])
            ->post('https://api.mercadopago.com/checkout/preferences', $preference);

        if (!$response->successful()) {
            $sell->update(['status' => 'failure']);
            $order->update(['payment_status' => 'failure']);

            return response()->json([
                'message' => 'Não foi possível criar o pagamento no Mercado Pago.',
            ], 502);
        }

        $data = $response->json();
        $sell->update([
            'status' => 'pending',
            'mercadopago_preference_id' => $data['id'] ?? null,
            'mercadopago_response' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
        $order->update(['payment_status' => 'pending']);

        return response()->json([
            'init_point' => $data['init_point'] ?? null,
            'sandbox_init_point' => $data['sandbox_init_point'] ?? null,
            'sandbox' => ($store->mercadopago_environment ?? 'production') === 'sandbox',
        ]);
    }

    public function createMercadoPagoPreference(Request $request, ShippingService $shippingService)
    {

        $validated = $request->validate([
            'store_id' => ['required', 'integer', 'exists:users,id'],
            'items' => ['array'], // opcional se derivar do carrinho
            'success_url' => ['nullable', 'url'],
            'failure_url' => ['nullable', 'url'],
            'pending_url' => ['nullable', 'url'],
        ]);

        $store = User::findOrFail($validated['store_id']);
        $credentials = $store->mercadopagoCredentials();

        if (!$credentials['access_token'] || !$credentials['public_key']) {
            return response()->json([
                'message' => 'Loja não configurou Mercado Pago.',
            ], 422);
        }

        $client = auth('client')->user();
        if (!$client) {
            return response()->json([
                'message' => 'Entre na sua conta para finalizar a compra.',
                'requires_auth' => true,
            ], 401);
        }

        $cart = $client->getCartForStore($store);
        $cartItems = $cart->items()
            ->with('product')
            ->orderBy('id')
            ->get()
            ->filter(fn ($item): bool => $item->type_of_sell !== 'encomenda')
            ->values();
        $usesCartItems = empty($validated['items'] ?? []);

        if ($usesCartItems) {
            foreach ($cartItems as $cartItem) {
                $availableTypes = $cartItem->product->type_of_sell ?? [];

                if ($availableTypes !== [] && !in_array($cartItem->type_of_sell, $availableTypes, true)) {
                    return response()->json([
                        'message' => 'Escolha a modalidade de compra para todos os produtos do carrinho.',
                    ], 422);
                }
            }
        }

        $items = $validated['items'] ?? [];

        // Carregar itens do carrinho do cliente autenticado, se não enviados
        if ($usesCartItems) {
            $items = $cartItems->map(function ($item) {
                return [
                    'id' => (string) $item->product_id,
                    'title' => $item->product->name . ($item->type_of_sell
                        ? ' - ' . match ($item->type_of_sell) {
                            'venda_por_entrega' => 'Venda por entrega',
                            'venda_no_local' => 'Venda no local',
                            'encomenda' => 'Encomenda',
                            default => $item->type_of_sell,
                        }
                        : ''),
                    'quantity' => (int) $item->quantity,
                    'currency_id' => 'BRL',
                    'unit_price' => (float) $item->price,
                ];
            })->toArray();
        }

        if (empty($items)) {
            return response()->json([
                'message' => 'Nenhum item encontrado para criar o pagamento.',
            ], 422);
        }

        $shippingData = null;
        $selectedShipping = session()->get('shipping.selected.' . $store->id);
        if ($selectedShipping !== null) {
            if (!$usesCartItems || !is_array($selectedShipping)) {
                return response()->json([
                    'message' => 'Recalcule o frete para os itens atuais do carrinho.',
                ], 422);
            }

            $shippingItems = $cartItems->map(function ($item): array {
                return [
                    'id' => (string) $item->product_id,
                    'width' => (float) $item->product->width_cm,
                    'height' => (float) $item->product->height_cm,
                    'length' => (float) $item->product->length_cm,
                    'weight' => (float) $item->product->weight_kg,
                    'insurance_value' => (float) $item->price,
                    'quantity' => (int) $item->quantity,
                ];
            })->all();
            $destinationPostalCode = $selectedShipping['destination_postal_code'] ?? '';

            if (
                (int) ($selectedShipping['store_id'] ?? 0) !== $store->id
                || !is_string($destinationPostalCode)
                || preg_match('/^\d{8}$/', $destinationPostalCode) !== 1
                || ($selectedShipping['items_signature'] ?? null) !== $shippingService->itemsSignature($shippingItems)
            ) {
                session()->forget('shipping.selected.' . $store->id);

                return response()->json([
                    'message' => 'O carrinho foi alterado. Calcule o frete novamente.',
                ], 422);
            }

            $availableShipping = $shippingService->calculate($destinationPostalCode, $shippingItems);
            $selectedOption = collect($availableShipping)->first(
                fn (array $option): bool =>
                    (string) ($option['id'] ?? '') === (string) ($selectedShipping['id'] ?? '')
            );

            if (!is_array($selectedOption)) {
                session()->forget('shipping.selected.' . $store->id);

                return response()->json([
                    'message' => 'A opção de frete não está mais disponível. Calcule novamente.',
                ], 422);
            }

            if (
                !is_numeric($selectedShipping['custom_price'] ?? null)
                || round((float) $selectedShipping['custom_price'], 2)
                    !== round((float) $selectedOption['custom_price'], 2)
            ) {
                session()->forget('shipping.selected.' . $store->id);

                return response()->json([
                    'message' => 'O preço do frete mudou. Calcule e selecione uma opção novamente.',
                ], 422);
            }

            $shippingPrice = (float) $selectedOption['custom_price'];
            $shippingCompany = (string) data_get($selectedOption, 'company.name', 'Transportadora');
            $shippingName = (string) ($selectedOption['name'] ?? 'Frete');
            $shippingData = [
                'service_id' => (string) $selectedOption['id'],
                'service_name' => $shippingName,
                'company_name' => $shippingCompany,
                'price' => $shippingPrice,
                'delivery_time' => $selectedOption['custom_delivery_time']
                    ?? $selectedOption['delivery_time']
                    ?? null,
                'delivery_range' => $selectedOption['custom_delivery_range']
                    ?? $selectedOption['delivery_range']
                    ?? null,
                'destination_postal_code' => $destinationPostalCode,
            ];
            $items[] = [
                'id' => 'shipping-' . $selectedOption['id'],
                'title' => 'Frete - ' . $shippingCompany . ' - ' . $shippingName,
                'quantity' => 1,
                'currency_id' => 'BRL',
                'unit_price' => $shippingPrice,
            ];
        }

        $payer = [
            'name' => $client->name,
            'email' => $client->email,
        ];

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

        // Cria o carrinho histórico e a venda antes de enviar ao MP para vincular na preferência
        [$sell, $oldCart, $cachedPreference] = DB::transaction(function () use (
            $cart,
            $cartItems,
            $client,
            $store,
            $shippingData
        ): array {
            Cart::query()->whereKey($cart->id)->lockForUpdate()->first();

            $cartSnapshot = $cartItems->map(fn ($item): array => [
                'product_id' => (int) $item->product_id,
                'quantity' => (int) $item->quantity,
                'price' => number_format((float) $item->price, 2, '.', ''),
                'type_of_sell' => $item->type_of_sell,
            ])->values()->all();

            $pendingSales = Sell::query()
                ->where('client_id', $client->id)
                ->where('user_id', $store->id)
                ->whereIn('status', ['pending', 'failure'])
                ->latest()
                ->get();
            $sell = $pendingSales->first(
                fn (Sell $candidate): bool => !Order::where('sell_id', $candidate->id)->exists()
            );
            $oldCart = $sell ? OldCart::find($sell->old_cart_id) : null;
            $cachedPreference = null;
            $sameCheckout = false;

            if (!$sell || !$oldCart) {
                $oldCart = OldCart::create([
                    'client_id' => $client->id,
                    'user_id' => $store->id,
                ]);

                $sell = Sell::create([
                    'client_id' => $client->id,
                    'user_id' => $store->id,
                    'old_cart_id' => $oldCart->id,
                    'total' => $cart->total + (float) ($shippingData['price'] ?? 0),
                    'status' => 'pending',
                    'shipping_data' => $shippingData,
                ]);
            } else {
                $existingSnapshot = $oldCart->items()->orderBy('id')->get()->map(fn ($item): array => [
                    'product_id' => (int) $item->product_id,
                    'quantity' => (int) $item->quantity,
                    'price' => number_format((float) $item->price, 2, '.', ''),
                    'type_of_sell' => $item->type_of_sell,
                ])->values()->all();
                $sameCheckout = $existingSnapshot === $cartSnapshot
                    && $sell->shipping_data === $shippingData;

                if ($sameCheckout) {
                    $cachedPreference = json_decode((string) $sell->mercadopago_response, true);
                }

                $oldCart->items()->delete();
                $sell->update([
                    'total' => $cart->total + (float) ($shippingData['price'] ?? 0),
                    'status' => 'pending',
                    'shipping_data' => $shippingData,
                    'mercadopago_response' => $sameCheckout ? $sell->mercadopago_response : null,
                ]);
            }

            foreach ($cartItems as $item) {
                OldCartItem::create([
                    'old_cart_id' => $oldCart->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'type_of_sell' => $item->type_of_sell,
                ]);
            }

            return [$sell, $oldCart, $sameCheckout ? $cachedPreference : null];
        });

        if (is_array($cachedPreference) && !empty($cachedPreference['init_point'])) {
            return response()->json([
                'init_point' => $cachedPreference['init_point'],
                'sandbox_init_point' => $cachedPreference['sandbox_init_point'] ?? null,
                'public_key' => $credentials['public_key'],
                'sandbox' => ($store->mercadopago_environment ?? 'production') === 'sandbox',
            ]);
        }

        $externalReference = (string) $sell->id;
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
            'notification_url' => $credentials['webhook_url']
                ?: route('mercadopago.webhook', [
                    'store' => $store->id,
                    'environment' => $store->mercadopago_environment ?? 'production',
                ]),
            'statement_descriptor' => $descriptor,
            'external_reference' => $externalReference,
            'metadata' => [
                'store_id' => $store->id,
                'client_id' => $client->id,
                'sell_id' => $sell->id,
                'old_cart_id' => $oldCart->id,
                'cart_id' => $cart->id,
            ],
            'binary_mode' => true,
        ];

        $baseUrl = 'https://api.mercadopago.com';
        $token = $credentials['access_token'];

        $headers = []; // sem integrator-id, não há coluna correspondente

        $preferenceRequest = Http::withToken($token)->withHeaders($headers);
        $response = $sell->mercadopago_preference_id
            ? $preferenceRequest->put(
                $baseUrl . '/checkout/preferences/' . $sell->mercadopago_preference_id,
                $preference
            )
            : $preferenceRequest->post($baseUrl . '/checkout/preferences', $preference);

        if (!$response->successful()) {
            $body = $response->json();
            $detail = $body['message'] ?? ($body['error'] ?? null);
            $sell->update(['status' => 'failure']);

            return response()->json([
                'message' => $detail ? ('Erro ao criar preferência no Mercado Pago: ' . $detail) : 'Erro ao criar preferência no Mercado Pago.',
                'error' => $body,
                'status' => $response->status(),
            ], 500);
        }

        $data = $response->json();

        $sell->update([
            'mercadopago_preference_id' => $data['id'] ?? $sell->mercadopago_preference_id,
            'mercadopago_response' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);

        return response()->json([
            'init_point' => $data['init_point'] ?? null,
            'sandbox_init_point' => $data['sandbox_init_point'] ?? null,
            'public_key' => $credentials['public_key'],
            'sandbox' => ($store->mercadopago_environment ?? 'production') === 'sandbox',
        ]);
    }

    public function webhook(Request $request, $store = null)
    {
        Log::info('Mercado Pago Webhook recebido:', [
            'store_param' => $store instanceof User ? $store->id : $store,
            'payload' => $request->all(),
            'query' => $request->query(),
        ]);

        // 1. Extração do Payment ID
        // Exemplo webhook V2:
        // {
        //   "action": "payment.created",
        //   "api_version": "v1",
        //   "data": { "id": "188120936197" },
        //   "date_created": "2026-11-12T11:33:24Z",
        //   "id": 45730672958036580,
        //   "live_mode": true,
        //   "type": "payment",
        //   "user_id": "2653728447"
        // }
        $paymentId = $request->input('data.id')
            ?? $request->query('data.id')
            ?? $request->query('data_id')
            ?? ($request->input('type') === 'payment' ? $request->input('id') : null)
            ?? ($request->query('topic') === 'payment' ? $request->query('id') : null)
            ?? $request->query('id')
            ?? $request->input('id');

        if (!$paymentId) {
            Log::info('Webhook recebido sem paymentId (possível ping de teste ou outro tópico)', [
                'payload' => $request->all()
            ]);
            return response()->json(['status' => 'ignored', 'message' => 'Nenhum payment ID encontrado'], 200);
        }

        // 2. Identificação da Loja (User)
        $storeModel = null;
        if ($store instanceof User) {
            $storeModel = $store;
        } elseif (!empty($store)) {
            $storeModel = is_numeric($store)
                ? User::find($store)
                : User::where('slug', $store)->first();
        }

        // Se não identificado pela URL, tenta localizar pelo user_id do payload (Mercado Pago User ID / Collector ID)
        if (!$storeModel && $request->filled('user_id')) {
            $mpUserId = (string) $request->input('user_id');
            $storeModel = User::where(function ($query) use ($mpUserId) {
                $query->where('mercadopago_access_token', 'like', "%-{$mpUserId}")
                    ->orWhere('mercadopago_access_token', 'like', "%{$mpUserId}%")
                    ->orWhere('mercadopago_sandbox_access_token', 'like', "%-{$mpUserId}")
                    ->orWhere('mercadopago_sandbox_access_token', 'like', "%{$mpUserId}%");
            })->first();
        }

        // Fallback: verificar se existe venda prévia com esse payment_id
        if (!$storeModel) {
            $existingSell = Sell::where('mercadopago_payment_id', (string) $paymentId)->first();
            if ($existingSell && $existingSell->store) {
                $storeModel = $existingSell->store;
            }
        }

        // Fallback: pegar a loja padrão que possua access token do Mercado Pago
        if (!$storeModel) {
            $storeModel = User::whereNotNull('mercadopago_access_token')
                ->orWhereNotNull('mercadopago_sandbox_access_token')
                ->first();
        }

        $environment = $request->has('live_mode')
            ? ($request->boolean('live_mode') ? 'production' : 'sandbox')
            : $request->query('environment', $storeModel?->mercadopago_environment ?? 'production');
        $credentials = $storeModel?->mercadopagoCredentials($environment);

        if (!$storeModel || !$credentials['access_token']) {
            Log::warning('Webhook recebido mas loja sem token ou não localizada.', [
                'store_param' => $store,
                'payload' => $request->all(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Loja não configurada'], 200);
        }

        // 3. Consulta à API do Mercado Pago
        $accessToken = $credentials['access_token'];
        $detail = null;
        $mpStatus = null;
        $dateApproved = null;
        $expirationDate = null;

        try {
            $resp = Http::withToken($accessToken)
                ->timeout(15)
                ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

            if ($resp->successful()) {
                $detail = $resp->json();
                $mpStatus = data_get($detail, 'status');
                $dateApproved = data_get($detail, 'date_approved') ?? data_get($detail, 'date_created');
                $expirationDate = data_get($detail, 'date_of_expiration');
            } else {
                Log::warning('Mercado Pago retornou erro ao consultar pagamento:', [
                    'status' => $resp->status(),
                    'body' => $resp->body(),
                    'payment_id' => $paymentId,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Erro ao conectar na API do Mercado Pago: ' . $e->getMessage());
        }

        // Determinação do status e dados a salvar
        // Quando o webhook do mercado pago é lido para este pagamento, o sistema aprova o pagamento
        $finalStatus = ($mpStatus === 'approved' || empty($mpStatus)) ? 'approved' : $mpStatus;
        $responsePayload = $detail ?? $request->all();
        $dateCreated = $request->input('date_created');
        $paymentDate = $dateApproved ?? $dateCreated ?? now();

        // 4. Localização da Venda (Sell)
        $sell = null;

        // A. Já vinculado a esse payment_id
        $sell = Sell::where('mercadopago_payment_id', (string) $paymentId)->first();

        // B. Pelo sell_id no metadata retornado pela API do MP
        if (!$sell && !empty(data_get($detail, 'metadata.sell_id'))) {
            $sell = Sell::where('id', data_get($detail, 'metadata.sell_id'))
                ->where('user_id', $storeModel->id)
                ->first();
        }

        // C. Pelo external_reference (Sell ID, OldCart ID ou Cart ID)
        $externalRef = data_get($detail, 'external_reference') ?? $request->input('external_reference');
        if (!$sell && !empty($externalRef)) {
            $sell = Sell::where('id', $externalRef)->where('user_id', $storeModel->id)->first();

            if (!$sell) {
                $sell = Sell::where('old_cart_id', $externalRef)->where('user_id', $storeModel->id)->first();
            }

            if (!$sell) {
                $cart = Cart::find($externalRef);
                if ($cart) {
                    $sell = Sell::where('user_id', $storeModel->id)
                        ->where('client_id', $cart->client_id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                }
            }
        }

        // D. Pelo preference_id ou order.id
        $preferenceId = data_get($detail, 'preference_id')
            ?? data_get($detail, 'order.id')
            ?? $request->input('preference_id');
        if (!$sell && !empty($preferenceId)) {
            $sell = Sell::where('mercadopago_preference_id', $preferenceId)->first();
        }

        // E. Pelo old_cart_id no metadata
        if (!$sell && !empty(data_get($detail, 'metadata.old_cart_id'))) {
            $sell = Sell::where('old_cart_id', data_get($detail, 'metadata.old_cart_id'))->first();
        }

        // F. Fallback: última venda pendente da loja (e cliente, se identificado)
        if (!$sell) {
            $clientId = data_get($detail, 'metadata.client_id');
            $query = Sell::where('user_id', $storeModel->id);
            if ($clientId) {
                $query->where('client_id', $clientId);
            }
            $sell = (clone $query)->where('status', 'pending')->latest()->first()
                ?? $query->latest()->first();
        }

        if (!$sell) {
            Log::warning("Webhook processado, mas nenhuma venda foi encontrada.", [
                'store_id' => $storeModel->id,
                'payment_id' => $paymentId,
                'payload' => $request->all(),
            ]);
            return response()->json([
                'status' => 'not_found',
                'message' => 'Nenhuma venda encontrada para associar ao pagamento',
                'payment_id' => $paymentId,
            ], 200);
        }

        // 5. Salva os dados conforme o webhook e aprova no sistema
        $carbonPaymentDate = null;
        if ($paymentDate) {
            try {
                $carbonPaymentDate = \Carbon\Carbon::parse($paymentDate)->setTimezone(config('app.timezone', 'America/Sao_Paulo'));
            } catch (\Throwable $e) {
                $carbonPaymentDate = now();
            }
        } else {
            $carbonPaymentDate = now();
        }

        $carbonExpirationDate = null;
        if ($expirationDate) {
            try {
                $carbonExpirationDate = \Carbon\Carbon::parse($expirationDate)->setTimezone(config('app.timezone', 'America/Sao_Paulo'));
            } catch (\Throwable $e) {
                $carbonExpirationDate = null;
            }
        }

        $sell->update([
            'status' => $finalStatus,
            'mercadopago_status' => $mpStatus ?: $finalStatus,
            'mercadopago_payment_id' => (string) $paymentId,
            'data_pagamento' => $carbonPaymentDate,
            'expiration_date' => $carbonExpirationDate,
            'mercadopago_response' => json_encode($responsePayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);

        Order::where('sell_id', $sell->id)->update(['payment_status' => $finalStatus]);

        // Se o status for aprovado, limpa os itens do carrinho ativo do cliente
        if ($finalStatus === 'approved' && !Order::where('sell_id', $sell->id)->exists()) {
            $activeCart = Cart::where('client_id', $sell->client_id)
                ->where('user_id', $sell->user_id)
                ->first();

            if ($activeCart) {
                $activeCart->items()->delete();
            }
        }

        Log::info("Venda #{$sell->id} atualizada e aprovada com sucesso via webhook do Mercado Pago.", [
            'sell_id' => $sell->id,
            'payment_id' => $paymentId,
            'status' => $sell->status,
            'mercadopago_status' => $sell->mercadopago_status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pagamento aprovado com sucesso',
            'sell_id' => $sell->id,
            'payment_id' => (string) $paymentId,
            'payment_status' => $sell->status,
        ], 200);
    }

    public function callbackSuccess(Request $request)
    {
        Log::info('Mercado Pago Callback Success:', $request->all());

        $paymentId = $request->input('payment_id') ?? $request->input('collection_id');
        $reference = $request->input('external_reference');

        if ($paymentId || $reference) {
            $this->finalizeOrder($reference, $paymentId, 'approved', $request->all());
        }

        return $this->handleCallback($request, 'success');
    }

    public function callbackFailure(Request $request)
    {
        Log::info('Mercado Pago Callback Failure:', $request->all());

        $paymentId = $request->input('payment_id') ?? $request->input('collection_id');
        $reference = $request->input('external_reference');

        if ($paymentId || $reference) {
            $this->finalizeOrder($reference, $paymentId, 'failure', $request->all());
        }

        return $this->handleCallback($request, 'failure');
    }

    public function callbackPending(Request $request)
    {
        Log::info('Mercado Pago Callback Pending:', $request->all());

        $paymentId = $request->input('payment_id') ?? $request->input('collection_id');
        $reference = $request->input('external_reference');

        if ($paymentId || $reference) {
            $this->finalizeOrder($reference, $paymentId, 'pending', $request->all());
        }

        return $this->handleCallback($request, 'pending');
    }

    private function handleCallback(Request $request, string $status)
    {
        $redirectUrl = '/';

        if ($request->filled('external_reference')) {
            $ref = $request->input('external_reference');
            $sell = Sell::find($ref);
            if ($sell && $sell->store) {
                $redirectUrl = route('store.show', ['slug' => $sell->store->slug]);
            } else {
                $cart = Cart::find($ref);
                if ($cart && $cart->store) {
                    $redirectUrl = route('store.show', ['slug' => $cart->store->slug]);
                }
            }
        }

        return redirect($redirectUrl)->with('status', 'Status do pagamento: ' . $status);
    }

    /**
     * Finaliza o pedido atualizando a venda e limpando o carrinho
     */
    private function finalizeOrder($reference, $paymentId, $status = 'pending', $payload = null)
    {
        $sell = null;

        if ($paymentId) {
            $sell = Sell::where('mercadopago_payment_id', (string) $paymentId)->first();
        }

        if (!$sell && $reference) {
            $sell = Sell::find($reference);
            if (!$sell) {
                $sell = Sell::where('old_cart_id', $reference)->first();
            }
            if (!$sell) {
                $cart = Cart::find($reference);
                if ($cart) {
                    $sell = Sell::where('client_id', $cart->client_id)
                        ->where('user_id', $cart->user_id)
                        ->where('status', 'pending')
                        ->latest()
                        ->first();
                }
            }
        }

        if ($sell) {
            $updateData = [
                'status' => $status,
                'mercadopago_status' => $status,
            ];

            if ($paymentId) {
                $updateData['mercadopago_payment_id'] = (string) $paymentId;
            }

            if ($status === 'approved' && !$sell->data_pagamento) {
                $updateData['data_pagamento'] = now();
            }

            if ($payload) {
                $updateData['mercadopago_response'] = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            $sell->update($updateData);

            Order::where('sell_id', $sell->id)->update(['payment_status' => $status]);

            if ($status === 'approved' && !Order::where('sell_id', $sell->id)->exists()) {
                $cart = Cart::where('client_id', $sell->client_id)
                    ->where('user_id', $sell->user_id)
                    ->first();
                if ($cart) {
                    $cart->items()->delete();
                }
            }
        }
    }
}
