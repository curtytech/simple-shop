<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditTrailResource\Pages;
use App\Models\AuditTrail;
use App\Models\Client;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AuditTrailResource extends Resource
{
    protected static ?string $model = AuditTrail::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Auditoria';

    protected static ?string $modelLabel = 'Log de auditoria';

    protected static ?string $pluralModelLabel = 'Logs de auditoria';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'store'], true);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data e hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('actor')
                    ->label('Ator')
                    ->getStateUsing(fn (AuditTrail $record): string => static::formatModel(
                        $record->actor,
                        $record->actor_type,
                        $record->actor_id,
                    )),
                Tables\Columns\TextColumn::make('action')
                    ->label('Ação')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('auditable')
                    ->label('Registro afetado')
                    ->getStateUsing(fn (AuditTrail $record): string => static::formatModel(
                        $record->auditable,
                        $record->auditable_type,
                        $record->auditable_id,
                    )),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP de origem')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditTrails::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['actor', 'auditable']);
        $user = auth()->user();

        if ($user?->role === 'admin') {
            return $query;
        }

        if ($user?->role !== 'store') {
            return $query->whereNull('audit_trail.id');
        }

        return $query->where(function (Builder $query) use ($user): void {
            $query
                ->whereHasMorph('actor', [User::class], fn (Builder $actors) => $actors->whereKey($user->getKey()))
                ->orWhereHasMorph('actor', [Client::class], fn (Builder $actors) => $actors->where('user_id', $user->getKey()));
        });
    }

    private static function formatModel(?Model $model, string $type, int|string $id): string
    {
        $name = $model?->getAttribute('name');
        $label = filled($name) ? $name : class_basename($type);

        return "{$label} (#{$id})";
    }
}
