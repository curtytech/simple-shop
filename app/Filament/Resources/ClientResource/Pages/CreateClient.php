<?php

namespace App\Filament\Resources\ClientResource\Pages;

use App\Filament\Resources\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClient extends CreateRecord
{
    protected static string $resource = ClientResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Cliente criado com sucesso!';
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se não for admin, definir o user_id como o usuário logado
        if (auth()->user()->role !== 'admin') {
            $data['user_id'] = auth()->id();
        }
        
        return $data;
    }
}