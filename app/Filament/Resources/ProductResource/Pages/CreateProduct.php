<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se não for admin, definir o user_id como o usuário logado
        if (auth()->user()->role !== 'admin') {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
}