<?php

namespace App\Filament\Resources\SellResource\Pages;

use App\Filament\Resources\SellResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSell extends CreateRecord
{
    protected static string $resource = SellResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se não for admin, definir o user_id como o usuário logado
        if (auth()->user()->role !== 'admin') {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}