<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Client;
use App\Models\CartItem;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ShippingCalculator extends Component
{
    #[Locked]
    public int $storeId;

    public string $destinationPostalCode = '';

    public array $shippingOptions = [];

    public ?string $selectedOptionId = null;

    public ?string $errorMessage = null;

    public bool $hasCalculated = false;

    public function mount(int $storeId): void
    {
        $this->storeId = $storeId;

        $client = Auth::guard('client')->user();
        if ($client instanceof Client) {
            $this->destinationPostalCode = (string) $client->zipcode;
        }
    }

    public function calculate(ShippingService $shippingService): void
    {
        $this->resetErrorBag();
        $this->errorMessage = null;
        $this->hasCalculated = false;
        $this->shippingOptions = [];
        $this->selectedOptionId = null;
        session()->forget([$this->quoteSessionKey(), $this->selectedSessionKey()]);

        $this->destinationPostalCode = preg_replace('/\D+/', '', $this->destinationPostalCode) ?? '';
        $this->validate([
            'destinationPostalCode' => ['required', 'regex:/^\d{8}$/'],
        ], [
            'destinationPostalCode.required' => 'Informe o CEP de destino.',
            'destinationPostalCode.regex' => 'O CEP deve conter 8 dígitos numéricos.',
        ]);

        $missingConfiguration = [];
        if (!is_string(config('services.melhor_envio.token')) || trim(config('services.melhor_envio.token')) === '') {
            $missingConfiguration[] = 'MELHOR_ENVIO_TOKEN';
        }

        $originPostalCode = preg_replace(
            '/\D+/',
            '',
            (string) config('services.melhor_envio.origin_cep', '')
        );
        if (!is_string($originPostalCode) || preg_match('/^\d{8}$/', $originPostalCode) !== 1) {
            $missingConfiguration[] = 'MELHOR_ENVIO_ORIGIN_CEP';
        }

        if ($missingConfiguration !== []) {
            $this->errorMessage = 'Integração de frete não configurada. Defina no .env: '
                . implode(', ', $missingConfiguration)
                . '. Depois atualize a configuração do Laravel.';

            return;
        }

        $client = Auth::guard('client')->user();
        if (!$client instanceof Client) {
            $this->errorMessage = 'Entre na sua conta para calcular o frete do carrinho.';

            return;
        }

        $store = $this->store();
        $cartItems = $client->getCartForStore($store)
            ->items()
            ->with('product')
            ->orderBy('id')
            ->get();

        if ($cartItems->isEmpty()) {
            $this->errorMessage = 'Adicione produtos ao carrinho antes de calcular o frete.';

            return;
        }

        $items = $this->mapCartItems($cartItems);
        if ($items === null) {
            return;
        }

        $this->shippingOptions = $shippingService->calculate(
            $this->destinationPostalCode,
            $items
        );
        $this->hasCalculated = true;

        if ($this->shippingOptions === []) {
            $this->errorMessage = 'Não foi possível encontrar opções de frete para este carrinho e CEP.';
            session()->forget($this->quoteSessionKey());

            return;
        }

        session()->put($this->quoteSessionKey(), [
            'destination_postal_code' => $this->destinationPostalCode,
            'items_signature' => $shippingService->itemsSignature($items),
            'options' => $this->shippingOptions,
        ]);
    }

    public function selectShipping(ShippingService $shippingService): void
    {
        $this->resetErrorBag();
        $this->validate([
            'selectedOptionId' => ['required', 'string'],
        ]);

        $quote = session()->get($this->quoteSessionKey());
        $client = Auth::guard('client')->user();

        if (!$client instanceof Client || !is_array($quote)) {
            $this->errorMessage = 'Calcule novamente o frete antes de selecioná-lo.';

            return;
        }

        $cartItems = $client->getCartForStore($this->store())
            ->items()
            ->with('product')
            ->orderBy('id')
            ->get();
        $items = $this->mapCartItems($cartItems);

        if (
            $items === null
            || ($quote['destination_postal_code'] ?? null) !== $this->destinationPostalCode
            || ($quote['items_signature'] ?? null) !== $shippingService->itemsSignature($items)
        ) {
            $this->errorMessage = 'O carrinho foi alterado. Calcule o frete novamente.';
            $this->shippingOptions = [];
            $this->selectedOptionId = null;
            session()->forget([$this->quoteSessionKey(), $this->selectedSessionKey()]);

            return;
        }

        $option = collect($quote['options'] ?? [])
            ->first(fn (array $candidate): bool =>
                (string) ($candidate['id'] ?? '') === $this->selectedOptionId
            );

        if (!is_array($option)) {
            $this->errorMessage = 'A opção selecionada não está mais disponível. Calcule novamente.';
            session()->forget([$this->quoteSessionKey(), $this->selectedSessionKey()]);

            return;
        }

        $selection = [
            'store_id' => $this->storeId,
            'id' => (string) $option['id'],
            'name' => (string) ($option['name'] ?? ''),
            'company' => $option['company'] ?? [],
            'custom_price' => (float) $option['custom_price'],
            'delivery_time' => $option['custom_delivery_time'] ?? $option['delivery_time'] ?? null,
            'delivery_range' => $option['custom_delivery_range'] ?? $option['delivery_range'] ?? null,
            'destination_postal_code' => $quote['destination_postal_code'],
            'items_signature' => $quote['items_signature'],
        ];

        session()->put($this->selectedSessionKey(), $selection);
        $this->dispatch('shipping-selected', shipping: $selection);
    }

    public function updatedDestinationPostalCode(): void
    {
        $this->shippingOptions = [];
        $this->selectedOptionId = null;
        $this->errorMessage = null;
        $this->hasCalculated = false;
        session()->forget([$this->quoteSessionKey(), $this->selectedSessionKey()]);
    }

    public function updatedSelectedOptionId(): void
    {
        session()->forget($this->selectedSessionKey());
    }

    public function render()
    {
        return view('livewire.shipping-calculator');
    }

    /**
     * @param iterable<CartItem> $cartItems
     * @return array<int, array<string, int|float|string>>|null
     */
    private function mapCartItems(iterable $cartItems): ?array
    {
        $items = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;

            if (!$product || (int) $product->user_id !== $this->storeId) {
                $this->errorMessage = 'Um produto do carrinho não pertence a esta loja.';

                return null;
            }

            $dimensions = [
                'width' => $product->width_cm,
                'height' => $product->height_cm,
                'length' => $product->length_cm,
                'weight' => $product->weight_kg,
            ];

            foreach ($dimensions as $dimension => $value) {
                if (!is_numeric($value) || (float) $value <= 0) {
                    $this->errorMessage = 'A loja precisa cadastrar dimensões e peso válidos em todos os produtos do carrinho.';

                    return null;
                }
            }

            $items[] = [
                'id' => (string) $product->id,
                'width' => (float) $product->width_cm,
                'height' => (float) $product->height_cm,
                'length' => (float) $product->length_cm,
                'weight' => (float) $product->weight_kg,
                'insurance_value' => (float) $cartItem->price,
                'quantity' => (int) $cartItem->quantity,
            ];
        }

        return $items;
    }

    private function store(): User
    {
        return User::query()
            ->whereKey($this->storeId)
            ->where('role', 'store')
            ->firstOrFail();
    }

    private function quoteSessionKey(): string
    {
        return 'shipping.quote.' . $this->storeId;
    }

    private function selectedSessionKey(): string
    {
        return 'shipping.selected.' . $this->storeId;
    }
}
