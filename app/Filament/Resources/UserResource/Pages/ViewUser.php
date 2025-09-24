<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informações Pessoais')
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nome'),
                        Infolists\Components\TextEntry::make('slug')
                            ->label('Slug'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('E-mail')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('celphone')
                            ->label('Celular')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('slogan')
                            ->label('Slogan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('Configurações de Acesso')
                    ->schema([
                        Infolists\Components\TextEntry::make('role')
                            ->label('Função')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'danger',
                                'store' => 'warning',
                                'user' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'Administrador',
                                'store' => 'Loja',
                                'user' => 'Usuário',
                                default => $state,
                            }),
                        Infolists\Components\TextEntry::make('email_verified_at')
                            ->label('E-mail Verificado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não verificado'),
                    ])->columns(2),

                Infolists\Components\Section::make('Endereço')
                    ->schema([
                        Infolists\Components\TextEntry::make('address')
                            ->label('Endereço'),
                        Infolists\Components\TextEntry::make('number')
                            ->label('Número'),
                        Infolists\Components\TextEntry::make('reference_point')
                            ->label('Ponto de Referência'),
                        Infolists\Components\TextEntry::make('city')
                            ->label('Cidade'),
                        Infolists\Components\TextEntry::make('state')
                            ->label('Estado'),
                        Infolists\Components\TextEntry::make('country')
                            ->label('País'),
                        Infolists\Components\TextEntry::make('zipcode')
                            ->label('CEP'),
                    ])->columns(3),

                Infolists\Components\Section::make('Redes Sociais')
                    ->schema([
                        Infolists\Components\TextEntry::make('instagram')
                            ->label('Instagram')
                            ->url(fn ($state) => $state ? (str_starts_with($state, 'http') ? $state : "https://instagram.com/{$state}") : null)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('facebook')
                            ->label('Facebook')
                            ->url(fn ($state) => $state ? (str_starts_with($state, 'http') ? $state : "https://facebook.com/{$state}") : null)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('site')
                            ->label('Site')
                            ->url(fn ($state) => $state ? (str_starts_with($state, 'http') ? $state : "https://{$state}") : null)
                            ->openUrlInNewTab(),
                    ])->columns(3),

                Infolists\Components\Section::make('Imagens')
                    ->schema([
                        Infolists\Components\ImageEntry::make('logo')
                            ->label('Logo')
                            ->height(150)
                            ->width(150),
                        Infolists\Components\ImageEntry::make('banner')
                            ->label('Banner')
                            ->height(150)
                            ->width(300),
                    ])->columns(2),

                Infolists\Components\Section::make('Informações do Sistema')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i'),
                    ])->columns(2),
            ]);
    }
}