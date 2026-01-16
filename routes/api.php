<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Log;


Route::prefix('api/payments/mercadopago')->group(function () {
    Route::post('/preference', [PaymentController::class, 'createMercadoPagoPreference'])->name('mercadopago.preference');
});