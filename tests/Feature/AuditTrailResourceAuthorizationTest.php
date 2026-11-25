<?php

declare(strict_types=1);

use App\Filament\Resources\AuditTrailResource;
use App\Models\User;

it('allows administrators to view all audit logs', function (): void {
    $admin = new User(['role' => 'admin']);

    $this->actingAs($admin);

    expect(AuditTrailResource::canViewAny())->toBeTrue()
        ->and(AuditTrailResource::getEloquentQuery()->getBindings())->toBe([]);
});

it('limits stores to their own and their clients audit logs', function (): void {
    $store = new User(['role' => 'store']);
    $store->setAttribute('id', 42);

    $this->actingAs($store);

    expect(AuditTrailResource::canViewAny())->toBeTrue()
        ->and(AuditTrailResource::getEloquentQuery()->getBindings())
        ->toContain(User::class)
        ->toContain(42)
        ->toContain(App\Models\Client::class);
});

it('denies access to unsupported roles and prevents changes to audit logs', function (): void {
    $user = new User(['role' => 'customer']);
    $this->actingAs($user);

    expect(AuditTrailResource::canViewAny())->toBeFalse()
        ->and(AuditTrailResource::getEloquentQuery()->getBindings())->toBe([])
        ->and(AuditTrailResource::canCreate())->toBeFalse()
        ->and(AuditTrailResource::canDeleteAny())->toBeFalse();
});
