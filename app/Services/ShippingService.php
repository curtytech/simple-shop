<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ShippingService
{
    /**
     * @param array<int, array<string, int|float|string>> $items
     * @return array<int, array<string, mixed>>
     */
    public function calculate(
        string $destinationPostalCode,
        array $items,
        ?string $originPostalCode = null
    ): array {
        $destinationPostalCode = $this->normalizePostalCode($destinationPostalCode);
        $configuredOrigin = $originPostalCode ?? config('services.melhor_envio.origin_cep');
        if (!is_string($configuredOrigin)) {
            Log::error('Melhor Envio não foi configurado: CEP de origem ausente.');

            return [];
        }

        try {
            $originPostalCode = $this->normalizePostalCode($configuredOrigin);
        } catch (InvalidArgumentException $exception) {
            Log::error('Melhor Envio não foi configurado com um CEP de origem válido.', [
                'exception' => $exception->getMessage(),
            ]);

            return [];
        }

        $products = $this->normalizeItems($items);
        $token = config('services.melhor_envio.token');

        if (!is_string($token) || $token === '') {
            Log::error('Melhor Envio não foi configurado: token ausente.');

            return [];
        }

        $sandbox = (bool) config('services.melhor_envio.sandbox', true);
        $baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2'
            : 'https://melhorenvio.com.br/api/v2';
        $payload = [
            'from' => ['postal_code' => $originPostalCode],
            'to' => ['postal_code' => $destinationPostalCode],
            'products' => $products,
        ];
        $cacheKey = 'melhor_envio:shipment:' . hash('sha256', json_encode([
            'sandbox' => $sandbox,
            'payload' => $payload,
        ], JSON_THROW_ON_ERROR));

        try {
            return Cache::remember($cacheKey, now()->addHours(2), function () use (
                $baseUrl,
                $payload,
                $token,
                $destinationPostalCode
            ): array {
                $response = Http::acceptJson()
                    ->withToken($token)
                    ->withUserAgent(config('app.name') . ' - ' . config('app.url'))
                    ->timeout(20)
                    ->post($baseUrl . '/me/shipment/calculate', $payload);

                if (!$response->successful()) {
                    throw new RuntimeException(
                        'Melhor Envio respondeu com HTTP ' . $response->status() . '.'
                    );
                }

                $services = $response->json();
                if (!is_array($services)) {
                    throw new RuntimeException('A resposta do Melhor Envio possui formato inválido.');
                }

                if (isset($services['services']) && is_array($services['services'])) {
                    $services = $services['services'];
                }

                if (!blank($services['error'] ?? null)) {
                    throw new RuntimeException('Melhor Envio retornou um erro na cotação.');
                }

                $services = array_values(array_filter(
                    $services,
                    static fn (mixed $service): bool => is_array($service)
                        && blank($service['error'] ?? null)
                        && isset($service['id'])
                        && is_numeric($service['custom_price'] ?? null)
                        && (float) $service['custom_price'] >= 0
                ));

                usort(
                    $services,
                    static fn (array $first, array $second): int =>
                        (float) $first['custom_price'] <=> (float) $second['custom_price']
                );

                return $services;
            });
        } catch (Throwable $exception) {
            Log::error('Falha ao consultar opções de frete no Melhor Envio.', [
                'destination_postal_code' => $destinationPostalCode,
                'exception' => $exception->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @param array<int, array<string, int|float|string>> $items
     */
    public function itemsSignature(array $items): string
    {
        return hash('sha256', json_encode($items, JSON_THROW_ON_ERROR));
    }

    private function normalizePostalCode(string $postalCode): string
    {
        $normalized = preg_replace('/\D+/', '', $postalCode);

        if (!is_string($normalized) || preg_match('/^\d{8}$/', $normalized) !== 1) {
            throw new InvalidArgumentException('O CEP deve conter exatamente 8 dígitos.');
        }

        return $normalized;
    }

    /**
     * @param array<int, array<string, int|float|string>> $items
     * @return array<int, array<string, int|float|string>>
     */
    private function normalizeItems(array $items): array
    {
        if ($items === []) {
            throw new InvalidArgumentException('Informe ao menos um item para calcular o frete.');
        }

        $products = [];

        foreach (array_values($items) as $index => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("O item " . ($index + 1) . " deve ser um array.");
            }

            foreach (['width', 'height', 'length', 'weight', 'insurance_value', 'quantity'] as $field) {
                if (!isset($item[$field]) || !is_numeric($item[$field])) {
                    throw new InvalidArgumentException("O item " . ($index + 1) . " não possui {$field} válido.");
                }
            }

            $quantity = (int) $item['quantity'];
            if ((float) $item['quantity'] !== (float) $quantity) {
                throw new InvalidArgumentException("O item " . ($index + 1) . " possui quantidade inválida.");
            }
            $dimensions = [
                'width' => (float) $item['width'],
                'height' => (float) $item['height'],
                'length' => (float) $item['length'],
                'weight' => (float) $item['weight'],
                'insurance_value' => (float) $item['insurance_value'],
                'quantity' => $quantity,
            ];

            if (
                $dimensions['width'] <= 0
                || $dimensions['height'] <= 0
                || $dimensions['length'] <= 0
                || $dimensions['weight'] <= 0
                || $dimensions['insurance_value'] < 0
                || $quantity < 1
            ) {
                throw new InvalidArgumentException("O item " . ($index + 1) . " possui dimensões ou valores inválidos.");
            }

            $products[] = array_merge(
                ['id' => (string) ($item['id'] ?? $index + 1)],
                $dimensions
            );
        }

        return $products;
    }
}
