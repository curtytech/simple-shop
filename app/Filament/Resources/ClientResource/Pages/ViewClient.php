<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Str;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

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
                        Infolists\Components\ImageEntry::make('image')
                            ->label('Foto')
                            ->height(150)
                            ->width(150)
                            ->circular(),
                        Infolists\Components\TextEntry::make('name')
                            ->label('Nome'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('E-mail')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('celphone')
                            ->label('Celular')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Loja')
                            ->visible(fn () => auth()->user()->role === 'admin'),
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
                            ->url(fn ($state) => $state ? (Str::startsWith($state, 'http') ? $state : "https://instagram.com/" . ltrim($state, '@')) : null)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('facebook')
                            ->label('Facebook')
                            ->url(fn ($state) => $state ? (Str::startsWith($state, 'http') ? $state : "https://facebook.com/" . ltrim($state, '@')) : null)
                            ->openUrlInNewTab(),
                        Infolists\Components\TextEntry::make('site')
                            ->label('Site')
                            ->url(fn ($state) => $state ? (Str::startsWith($state, 'http') ? $state : "https://" . $state) : null)
                            ->openUrlInNewTab(),
                    ])->columns(3),

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