<?php

namespace App\Filament\Widgets;

use App\Models\Sell;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentSalesWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sell::query()
                    ->with(['client', 'store'])
                    ->when(
                        auth()->user()->role !== 'admin',
                        fn ($query) => $query->where('user_id', auth()->id()),
                    )
                    ->latest('created_at')
                    ->limit(5),
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Venda')
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->default('Cliente removido'),
                Tables\Columns\TextColumn::make('store.name')
                    ->label('Loja')
                    ->visible(fn (): bool => auth()->user()?->role === 'admin'),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved' => 'Aprovada',
                        'pending' => 'Pendente',
                        'failure' => 'Cancelada',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'failure' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false);
    }
}
