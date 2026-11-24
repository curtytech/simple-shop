<?php

namespace App\Filament\Resources\CustomerNotificationResource\Pages;

use App\Filament\Resources\CustomerNotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomerNotification extends EditRecord
{
    protected static string $resource = CustomerNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
