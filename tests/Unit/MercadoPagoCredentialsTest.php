<?php

use App\Models\User;

it('uses production credentials by default and sandbox credentials when selected', function (): void {
    $store = new User([
        'mercadopago_public_key' => 'production-public-key',
        'mercadopago_access_token' => 'production-access-token',
        'mercadopago_webhook_url' => 'https://production.example/webhook',
        'mercadopago_webhook_secret' => 'production-secret',
        'mercadopago_sandbox_public_key' => 'sandbox-public-key',
        'mercadopago_sandbox_access_token' => 'sandbox-access-token',
        'mercadopago_sandbox_webhook_url' => 'https://sandbox.example/webhook',
        'mercadopago_sandbox_webhook_secret' => 'sandbox-secret',
    ]);

    expect($store->mercadopagoCredentials())->toBe([
        'public_key' => 'production-public-key',
        'access_token' => 'production-access-token',
        'webhook_url' => 'https://production.example/webhook',
        'webhook_secret' => 'production-secret',
    ]);

    $store->mercadopago_environment = 'sandbox';

    expect($store->mercadopagoCredentials())->toBe([
        'public_key' => 'sandbox-public-key',
        'access_token' => 'sandbox-access-token',
        'webhook_url' => 'https://sandbox.example/webhook',
        'webhook_secret' => 'sandbox-secret',
    ])->and($store->mercadopago_sandbox)->toBeTrue();
});
