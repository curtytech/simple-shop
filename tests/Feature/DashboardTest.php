<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows registration and sales information on the Filament dashboard', function (): void {
    $store = User::where('email', 'user@user')->firstOrFail();

    $this->actingAs($store)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dados do cadastro')
        ->assertSee('Vendas registradas')
        ->assertSee('Vendas recentes')
        ->assertSee($store->email);
});

it('shows store registration totals to administrators', function (): void {
    $admin = User::where('email', 'admin@admin')->firstOrFail();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Lojas cadastradas')
        ->assertSee('Clientes cadastrados')
        ->assertSee('Faturamento aprovado');
});
