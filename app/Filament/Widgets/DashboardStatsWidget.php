<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Sell;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Resumo';

    protected function getStats(): array
    {
        $user = auth()->user();
        $sales = Sell::query()
            ->when($user->role !== 'admin', fn ($query) => $query->where('user_id', $user->id));
        $approvedSales = (clone $sales)->where('status', 'approved');
        $clients = Client::query()
            ->when($user->role !== 'admin', fn ($query) => $query->where('user_id', $user->id));

        $stats = [
            Stat::make('Clientes cadastrados', number_format($clients->count(), 0, ',', '.')),
        ];

        if ($user->role === 'admin') {
            array_unshift(
                $stats,
                Stat::make('Lojas cadastradas', number_format(User::query()->where('role', 'store')->count(), 0, ',', '.')),
            );
        }

        $stats[] = Stat::make('Vendas registradas', number_format((clone $sales)->count(), 0, ',', '.'));
        $stats[] = Stat::make('Vendas aprovadas', number_format((clone $approvedSales)->count(), 0, ',', '.'))
            ->color('success');
        $stats[] = Stat::make('Vendas pendentes', number_format((clone $sales)->where('status', 'pending')->count(), 0, ',', '.'))
            ->color('warning');
        $stats[] = Stat::make(
            'Faturamento aprovado',
            'R$ ' . number_format((float) (clone $approvedSales)->sum('total'), 2, ',', '.'),
        )->color('success');

        return $stats;
    }
}
