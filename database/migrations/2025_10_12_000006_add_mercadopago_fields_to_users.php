<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mercadopago_public_key')->nullable()->after('site');
            $table->string('mercadopago_access_token')->nullable()->after('mercadopago_public_key');
            $table->boolean('mercadopago_sandbox')->default(false)->after('mercadopago_access_token');
            $table->string('mercadopago_webhook_url')->nullable()->after('mercadopago_sandbox');
            $table->string('mercadopago_webhook_secret')->nullable()->after('mercadopago_webhook_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mercadopago_public_key', 'mercadopago_access_token', 'mercadopago_sandbox', 'mercadopago_webhook_url', 'mercadopago_webhook_secret']);
        });
    }
};
