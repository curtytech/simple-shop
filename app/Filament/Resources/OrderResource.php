<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Encomendas';
    protected static ?string $modelLabel = 'Encomenda';
    protected static ?string $pluralModelLabel = 'Encomendas';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pedido')
                ->schema([
                    Forms\Components\Placeholder::make('client_display')
                        ->label('Cliente')
                        ->content(fn (?Order $record): string => $record?->client?->name ?? '—'),
                    Forms\Components\Placeholder::make('product_display')
                        ->label('Produto')
                        ->content(fn (?Order $record): string => $record?->product?->name ?? '—'),
                    Forms\Components\TextInput::make('quantity')->label('Quantidade')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('total_price')->label('Total')->prefix('R$')->disabled()->dehydrated(false),
                ])->columns(2),
            Forms\Components\Section::make('Análise e andamento')
                ->description('Ao aceitar, a encomenda passa para preparação. A observação pode ser usada para registrar orientações e contato com o cliente.')
                ->schema([
                    Forms\Components\Toggle::make('approved')->label('Aprovada pelo administrador'),
                    Forms\Components\Select::make('status')
                        ->label('Status do pedido')
                        ->options([
                            'pendente' => 'Pendente de análise',
                            'em_preparacao' => 'Em preparação',
                            'pronto' => 'Pronto para entrega/retirada',
                            'entregue' => 'Entregue/retirado',
                            'rejeitado' => 'Rejeitado',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('observation')
                        ->label('Observação para gestão e comunicação')
                        ->rows(5)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Pedido')->sortable(),
                Tables\Columns\TextColumn::make('client.name')->label('Cliente')->searchable(),
                Tables\Columns\TextColumn::make('product.name')->label('Produto')->searchable(),
                Tables\Columns\TextColumn::make('quantity')->label('Quantidade'),
                Tables\Columns\TextColumn::make('total_price')->label('Total')->money('BRL')->sortable(),
                Tables\Columns\IconColumn::make('approved')->label('Aprovada')->boolean(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                    'pendente' => 'Pendente de análise',
                    'em_preparacao' => 'Em preparação',
                    'pronto' => 'Pronto',
                    'entregue' => 'Entregue/retirado',
                    'rejeitado' => 'Rejeitado',
                    default => $state,
                })->color(fn (string $state): string => match ($state) {
                    'pendente' => 'warning',
                    'rejeitado' => 'danger',
                    'entregue' => 'success',
                    default => 'primary',
                }),
                Tables\Columns\TextColumn::make('created_at')->label('Recebido em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('accept')
                    ->label('Aceitar')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === 'pendente')
                    ->action(fn (Order $record) => $record->update(['approved' => true, 'status' => 'em_preparacao'])),
                Tables\Actions\Action::make('reject')
                    ->label('Rejeitar')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Order $record): bool => $record->status === 'pendente')
                    ->action(fn (Order $record) => $record->update(['approved' => false, 'status' => 'rejeitado'])),
                Tables\Actions\EditAction::make(),
            ])
            ->recordUrl(fn (Order $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }
}
