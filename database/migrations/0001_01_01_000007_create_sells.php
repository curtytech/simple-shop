<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Loja
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 10, 2); // Total da venda
            $table->string('status')->default('pending'); // Status da venda
            $table->string('mercadopago_payment_id')->nullable();
            $table->string('mercadopago_preference_id')->nullable(); // ID da preferência do MP
            $table->string('mercadopago_status')->nullable();
            $table->dateTime('data_pagamento')->nullable();
            $table->text('mercadopago_response')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sells');
    }
};
