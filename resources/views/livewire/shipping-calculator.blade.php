<div class="space-y-4 text-gray-800 dark:text-gray-100">
    <h4 class="text-lg font-semibold">Calcular frete</h4>

    <form wire:submit="calculate" class="flex flex-col sm:flex-row gap-3">
        <label for="shipping-postal-code-{{ $storeId }}" class="sr-only">CEP de destino</label>
        <input
            id="shipping-postal-code-{{ $storeId }}"
            type="text"
            inputmode="numeric"
            autocomplete="postal-code"
            maxlength="9"
            wire:model="destinationPostalCode"
            placeholder="CEP de destino"
            class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
        >
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:target="calculate"
            class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 disabled:cursor-wait disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="calculate">Calcular</span>
            <span wire:loading wire:target="calculate">Calculando...</span>
        </button>
    </form>

    @error('destinationPostalCode')
        <p class="text-sm text-red-600" role="alert">{{ $message }}</p>
    @enderror
    @error('selectedOptionId')
        <p class="text-sm text-red-600" role="alert">Selecione uma opção de frete.</p>
    @enderror

    <p wire:loading wire:target="calculate" class="text-sm text-gray-600 dark:text-gray-300">
        Consultando transportadoras e prazos...
    </p>

    @if($errorMessage)
        <p class="rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/30 dark:text-red-200" role="alert">
            {{ $errorMessage }}
        </p>
    @endif

    @if($shippingOptions !== [])
        <fieldset class="space-y-3">
            <legend class="text-sm font-medium">Opções disponíveis</legend>

            @foreach($shippingOptions as $option)
                @php
                    $companyName = data_get($option, 'company.name', 'Transportadora');
                    $logo = data_get($option, 'company.picture');
                    $deliveryRange = $option['custom_delivery_range'] ?? $option['delivery_range'] ?? null;
                    $deliveryTime = $option['custom_delivery_time'] ?? $option['delivery_time'] ?? null;
                @endphp

                <label
                    wire:key="shipping-option-{{ $storeId }}-{{ $option['id'] }}"
                    class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-200 p-3 hover:border-blue-400 dark:border-gray-700 dark:hover:border-blue-500"
                >
                    <input
                        type="radio"
                        name="shipping-option-{{ $storeId }}"
                        value="{{ $option['id'] }}"
                        wire:model="selectedOptionId"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                    >

                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ $companyName }}" loading="lazy" class="h-10 w-14 object-contain">
                    @else
                        <span class="flex h-10 w-14 items-center justify-center rounded bg-gray-100 text-xs font-semibold dark:bg-gray-700">
                            {{ mb_substr($companyName, 0, 2) }}
                        </span>
                    @endif

                    <span class="min-w-0 flex-1">
                        <span class="block font-medium">{{ $companyName }}</span>
                        <span class="block text-sm text-gray-600 dark:text-gray-300">{{ $option['name'] ?? 'Serviço de entrega' }}</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">
                            @if(is_array($deliveryRange) && isset($deliveryRange['min'], $deliveryRange['max']))
                                {{ $deliveryRange['min'] }} a {{ $deliveryRange['max'] }} dias úteis
                            @elseif(is_numeric($deliveryTime))
                                {{ $deliveryTime }} dias úteis
                            @else
                                Prazo indisponível
                            @endif
                        </span>
                    </span>

                    <span class="whitespace-nowrap font-semibold">
                        R$ {{ number_format((float) $option['custom_price'], 2, ',', '.') }}
                    </span>
                </label>
            @endforeach
        </fieldset>

        <button
            type="button"
            wire:click="selectShipping"
            wire:loading.attr="disabled"
            wire:target="selectShipping"
            @disabled(!$selectedOptionId)
            class="w-full rounded-lg bg-green-600 px-4 py-2 font-medium text-white hover:bg-green-700 disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="selectShipping">Usar frete selecionado</span>
            <span wire:loading wire:target="selectShipping">Salvando...</span>
        </button>
    @endif

    @if(session()->has('shipping.selected.' . $storeId))
        @php($selectedShipping = session('shipping.selected.' . $storeId))
        <p class="text-sm text-green-700 dark:text-green-300" role="status">
            Frete selecionado: {{ $selectedShipping['name'] }} —
            R$ {{ number_format((float) $selectedShipping['custom_price'], 2, ',', '.') }}
        </p>
    @endif
</div>
