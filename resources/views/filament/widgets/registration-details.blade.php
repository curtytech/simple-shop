<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Dados do cadastro</x-slot>

        <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Telefone</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">{{ $user->celphone ?: 'Não informado' }}</dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Site</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">{{ $user->site ?: 'Não informado' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="font-medium text-gray-500 dark:text-gray-400">Endereço</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">
                    {{ collect([$user->address, $user->number, $user->city, $user->state, $user->zipcode])->filter()->implode(', ') ?: 'Não informado' }}
                </dd>
            </div>
            <div>
                <dt class="font-medium text-gray-500 dark:text-gray-400">Cadastro realizado em</dt>
                <dd class="mt-1 text-gray-950 dark:text-white">{{ $user->created_at?->format('d/m/Y') ?: 'Não informado' }}</dd>
            </div>
        </dl>
    </x-filament::section>
</x-filament-widgets::widget>
