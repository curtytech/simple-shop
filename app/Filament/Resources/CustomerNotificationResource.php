<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerNotificationResource\Pages;
use App\Models\Client;
use App\Models\CustomerNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CustomerNotificationResource extends Resource
{
    protected static ?string $model = CustomerNotification::class;
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notificações';
    protected static ?string $modelLabel = 'Notificação';
    protected static ?string $pluralModelLabel = 'Notificações';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('client_id')
                ->label('Cliente')
                ->options(fn () => Client::query()
                    ->when(auth()->user()->role !== 'admin', fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->orderBy('name')
                    ->pluck('name', 'id'))
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('title')
                ->label('Título')
                ->required()
                ->maxLength(255),
            Forms\Components\Textarea::make('message')
                ->label('Mensagem')
                ->required()
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\TextInput::make('action_url')
                ->label('Link de ação')
                ->url()
                ->maxLength(255),
            Forms\Components\DateTimePicker::make('read_at')
                ->label('Lida em'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')->label('Cliente')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('message')->label('Mensagem')->limit(70),
                Tables\Columns\IconColumn::make('read_at')
                    ->label('Lida')
                    ->getStateUsing(fn (CustomerNotification $record): bool => filled($record->read_at))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('Criada em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('read_at')->label('Lida'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->recordUrl(fn (CustomerNotification $record): string => static::getUrl('edit', ['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomerNotifications::route('/'),
            'create' => Pages\CreateCustomerNotification::route('/create'),
            'view' => Pages\ViewCustomerNotification::route('/{record}'),
            'edit' => Pages\EditCustomerNotification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->whereHas('client', fn (Builder $clients) => $clients->where('user_id', auth()->id()));
        }

        return $query;
    }
}
