<?php

declare(strict_types=1);

use App\Models\AuditTrail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('records the actor, action, time, origin and affected model', function (): void {
    $actor = User::create([
        'name' => 'Auditor',
        'slug' => 'auditor',
        'email' => 'auditor@example.com',
        'password' => 'password',
    ]);

    $auditable = User::create([
        'name' => 'Audited user',
        'slug' => 'audited-user',
        'email' => 'audited@example.com',
        'password' => 'password',
    ]);

    $entry = AuditTrail::record($actor, 'user.updated', $auditable, '192.0.2.10');

    expect($entry->actor->is($actor))->toBeTrue()
        ->and($entry->auditable->is($auditable))->toBeTrue()
        ->and($entry->action)->toBe('user.updated')
        ->and($entry->ip_address)->toBe('192.0.2.10')
        ->and($entry->created_at)->not->toBeNull()
        ->and(AuditTrail::count())->toBe(1);
});
