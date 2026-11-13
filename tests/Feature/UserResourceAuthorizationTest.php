<?php

declare(strict_types=1);

use App\Filament\Resources\UserResource;
use App\Models\User;

it('restricts a store account to viewing and editing its own store', function (): void {
    $store = new User(['role' => 'store']);
    $store->setAttribute('id', 10);
    $otherStore = new User(['role' => 'store']);
    $otherStore->setAttribute('id', 20);

    $this->actingAs($store);

    expect(UserResource::canViewAny())->toBeTrue()
        ->and(UserResource::canView($store))->toBeTrue()
        ->and(UserResource::canEdit($store))->toBeTrue()
        ->and(UserResource::canView($otherStore))->toBeFalse()
        ->and(UserResource::canEdit($otherStore))->toBeFalse()
        ->and(UserResource::canCreate())->toBeFalse()
        ->and(UserResource::canDelete($store))->toBeFalse()
        ->and(UserResource::canDeleteAny())->toBeFalse();

    expect(UserResource::getEloquentQuery()->getBindings())
        ->toContain('store')
        ->toContain(10);
});

it('keeps complete store management available to administrators', function (): void {
    $admin = new User(['role' => 'admin']);
    $admin->setAttribute('id', 1);
    $store = new User(['role' => 'store']);
    $store->setAttribute('id', 10);

    $this->actingAs($admin);

    expect(UserResource::canViewAny())->toBeTrue()
        ->and(UserResource::canCreate())->toBeTrue()
        ->and(UserResource::canView($store))->toBeTrue()
        ->and(UserResource::canEdit($store))->toBeTrue()
        ->and(UserResource::canDelete($store))->toBeTrue()
        ->and(UserResource::canDeleteAny())->toBeTrue();

    expect(UserResource::getEloquentQuery()->getBindings())->toBe([]);
});
