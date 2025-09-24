<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Client;

class ListClients extends ListRecords
{
    protected static string $resource = ClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Novo Cliente')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Todos')
                ->badge(Client::query()->when(auth()->user()->role !== 'admin', fn ($query) => $query->where('user_id', auth()->id()))->count()),
        ];

        // Adicionar tabs por estado se houver clientes
        $states = Client::query()
            ->when(auth()->user()->role !== 'admin', fn ($query) => $query->where('user_id', auth()->id()))
            ->whereNotNull('state')
            ->distinct()
            ->pluck('state')
            ->sort();

        foreach ($states as $state) {
            $tabs[$state] = Tab::make($state)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', $state))
                ->badge(Client::query()
                    ->when(auth()->user()->role !== 'admin', fn ($query) => $query->where('user_id', auth()->id()))
                    ->where('state', $state)
                    ->count());
        }

        return $tabs;
    }
}