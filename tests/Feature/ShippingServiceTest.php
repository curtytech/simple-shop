<?php

declare(strict_types=1);

use App\Services\ShippingService;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    config()->set('cache.default', 'array');
    config()->set('services.melhor_envio.token', 'test-token');
    config()->set('services.melhor_envio.sandbox', true);
    config()->set('services.melhor_envio.origin_cep', '01001-000');
    Cache::store('array')->flush();
});

it('calcula fretes usando as dimensões do item e retorna do menor para o maior preço', function (): void {
    Http::fake([
        '*' => Http::response([
            [
                'id' => 1,
                'name' => 'Expresso',
                'custom_price' => 25.90,
                'company' => ['name' => 'Transportadora A'],
            ],
            [
                'id' => 2,
                'name' => 'Econômico',
                'custom_price' => 12.50,
                'company' => ['name' => 'Transportadora B'],
            ],
            [
                'id' => 3,
                'name' => 'Indisponível',
                'custom_price' => 1.00,
                'error' => 'Serviço indisponível',
            ],
        ]),
    ]);

    $items = [[
        'id' => 'product-42',
        'width' => 15,
        'height' => 10,
        'length' => 20,
        'weight' => 0.8,
        'insurance_value' => 99.90,
        'quantity' => 2,
    ]];

    $services = app(ShippingService::class)->calculate('12345-678', $items);

    expect(array_column($services, 'id'))->toBe([2, 1]);

    Http::assertSent(function (Request $request): bool {
        $payload = $request->data();

        return $request->url() === 'https://sandbox.melhorenvio.com.br/api/v2/me/shipment/calculate'
            && data_get($payload, 'from.postal_code') === '01001000'
            && data_get($payload, 'to.postal_code') === '12345678'
            && data_get($payload, 'products.0.width') === 15
            && data_get($payload, 'products.0.quantity') === 2;
    });
});

it('reutiliza a cotação em cache por duas horas para os mesmos parâmetros', function (): void {
    Http::fake([
        '*' => Http::response([[
            'id' => 1,
            'name' => 'Expresso',
            'custom_price' => 25.90,
        ]]),
    ]);

    $items = [[
        'id' => 'product-42',
        'width' => 15,
        'height' => 10,
        'length' => 20,
        'weight' => 0.8,
        'insurance_value' => 99.90,
        'quantity' => 1,
    ]];

    $service = app(ShippingService::class);
    $service->calculate('12345678', $items);
    $service->calculate('12345678', $items);

    Http::assertSentCount(1);
});

it('usa a URL de produção e retorna uma lista vazia quando a API falha', function (): void {
    config()->set('services.melhor_envio.sandbox', false);
    Http::fake([
        '*' => Http::response(['message' => 'temporariamente indisponível'], 503),
    ]);

    $items = [[
        'id' => 'product-42',
        'width' => 15,
        'height' => 10,
        'length' => 20,
        'weight' => 0.8,
        'insurance_value' => 99.90,
        'quantity' => 1,
    ]];

    expect(app(ShippingService::class)->calculate('12345678', $items))->toBe([]);

    Http::assertSent(fn (Request $request): bool =>
        $request->url() === 'https://melhorenvio.com.br/api/v2/me/shipment/calculate'
    );
});
