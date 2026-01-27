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
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Quantidade vendida
            $table->string('status')->default('pending'); // Status da venda
            $table->decimal('price', 10, 2); // Preço no momento da adição
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
