<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class RegistrationDetailsWidget extends Widget
{
    protected static string $view = 'filament.widgets.registration-details';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->check();
    }

    protected function getViewData(): array
    {
        return [
            'user' => auth()->user(),
        ];
    }
}
