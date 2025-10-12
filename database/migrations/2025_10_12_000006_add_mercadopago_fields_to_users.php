<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mp_public_key')->nullable()->after('site');
            $table->string('mp_access_token')->nullable()->after('mp_public_key');
            $table->boolean('mp_sandbox')->default(false)->after('mp_access_token');
            $table->string('mp_integrator_id')->nullable()->after('mp_sandbox');
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mp_public_key','mp_access_token','mp_sandbox','mp_integrator_id']);
        });
    }
};