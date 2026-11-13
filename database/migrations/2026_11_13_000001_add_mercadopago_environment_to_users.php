<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mercadopago_environment')->default('production')->after('mercadopago_sandbox');
            $table->string('mercadopago_sandbox_public_key')->nullable()->after('mercadopago_webhook_secret');
            $table->string('mercadopago_sandbox_access_token')->nullable()->after('mercadopago_sandbox_public_key');
            $table->string('mercadopago_sandbox_webhook_url')->nullable()->after('mercadopago_sandbox_access_token');
            $table->string('mercadopago_sandbox_webhook_secret')->nullable()->after('mercadopago_sandbox_webhook_url');
        });

        DB::table('users')
            ->where('mercadopago_sandbox', true)
            ->update([
                'mercadopago_environment' => 'sandbox',
                'mercadopago_sandbox_public_key' => DB::raw('mercadopago_public_key'),
                'mercadopago_sandbox_access_token' => DB::raw('mercadopago_access_token'),
                'mercadopago_sandbox_webhook_url' => DB::raw('mercadopago_webhook_url'),
                'mercadopago_sandbox_webhook_secret' => DB::raw('mercadopago_webhook_secret'),
            ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mercadopago_environment',
                'mercadopago_sandbox_public_key',
                'mercadopago_sandbox_access_token',
                'mercadopago_sandbox_webhook_url',
                'mercadopago_sandbox_webhook_secret',
            ]);
        });
    }
};
