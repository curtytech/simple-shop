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

        if ($items === []) {
            throw new InvalidArgumentException('Informe ao menos um item para calcular o frete.');
        }

        $token = config('services.melhor_envio.token');

        if (!is_string($token) || $token === '') {
            Log::error('Melhor Envio não foi configurado: token ausente.');

            return [];
        }

        try {
            $package = $this->configuredPackage();
        } catch (InvalidArgumentException $exception) {
            Log::error('Melhor Envio não foi configurado com as dimensões do pacote.', [
                'exception' => $exception->getMessage(),
            ]);

            return [];
        }

        $sandbox = (bool) config('services.melhor_envio.sandbox', true);
        $baseUrl = $sandbox
            ? 'https://sandbox.melhorenvio.com.br/api/v2'
            : 'https://melhorenvio.com.br/api/v2';
        $payload = $this->createPayload($destinationPostalCode, $originPostalCode, $items, $package);
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
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->withUserAgent(config('services.melhor_envio.user_agent'))
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
     * @return array{
     *     from: array{postal_code: string},
     *     to: array{postal_code: string},
     *     products: array<int, array<string, int|float|string>>,
     *     package: array{height: float, width: float, length: float, weight: float}
     * }
     */
    public function buildPayload(string $destinationPostalCode, array $items): array
    {
        $originPostalCode = config('services.melhor_envio.origin_cep');
        if (!is_string($originPostalCode)) {
            throw new InvalidArgumentException('Informe um CEP de origem válido.');
        }

        if ($items === []) {
            throw new InvalidArgumentException('Informe ao menos um item para calcular o frete.');
        }

        return $this->createPayload(
            $this->normalizePostalCode($destinationPostalCode),
            $this->normalizePostalCode($originPostalCode),
            $items,
            $this->configuredPackage()
        );
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
     * @return array{height: float, width: float, length: float, weight: float}
     */
    private function configuredPackage(): array
    {
        $package = [];

        foreach (['height', 'width', 'length', 'weight'] as $dimension) {
            $value = config('services.melhor_envio.package.' . $dimension);
            if (!is_numeric($value) || (float) $value <= 0) {
                throw new InvalidArgumentException(
                    'A dimensão ' . $dimension . ' deve ser um número maior que zero.'
                );
            }

            $package[$dimension] = (float) $value;
        }

        return $package;
    }

    /**
     * @param array<int, array<string, int|float|string>> $items
     * @param array{height: float, width: float, length: float, weight: float} $package
     * @return array{
     *     from: array{postal_code: string},
     *     to: array{postal_code: string},
     *     products: array<int, array<string, int|float|string>>,
     *     package: array{height: float, width: float, length: float, weight: float}
     * }
     */
    private function createPayload(
        string $destinationPostalCode,
        string $originPostalCode,
        array $items,
        array $package
    ): array {
        return [
            'from' => ['postal_code' => $originPostalCode],
            'to' => ['postal_code' => $destinationPostalCode],
            'products' => $items,
            'package' => $package,
        ];
    }
}
