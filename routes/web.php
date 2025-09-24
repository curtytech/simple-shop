<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClientAuthController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

// Rota para buscar store_id por slug
Route::get('/api/store-by-slug/{slug}', function ($slug) {
    $store = User::where('slug', $slug)->first();
    
    if ($store) {
        return response()->json([
            'success' => true,
            'store_id' => $store->id
        ]);
    }
    
    return response()->json([
        'success' => false,
        'message' => 'Loja não encontrada'
    ], 404);
});

// Rotas de autenticação de clientes
Route::prefix('client')->group(function () {
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('client.login');
    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('client.register');
    Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login.post');
    Route::post('/register', [ClientAuthController::class, 'register'])->name('client.register.post');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');
    Route::get('/user', [ClientAuthController::class, 'user'])->name('client.user');
});

// Rotas do carrinho (API) - protegidas por autenticação de cliente
Route::prefix('api/cart')->middleware('client.auth')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/update', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/remove', [CartController::class, 'removeFromCart'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clearCart'])->name('cart.clear');
    Route::get('/get', [CartController::class, 'getCart'])->name('cart.get');
});

Route::get('/{slug}', [StoreController::class, 'show'])->name('store.show');

