<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->unique();
            $table->string('slogan')->nullable();
            $table->string('celphone')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        User::firstOrCreate(
            ['email' => 'admin@admin'],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'slogan' => 'Administrator',
                'celphone' => '000-000-0000',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'admin',
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@user'],
            [
                'name' => 'store',
                'slug' => 'store',
                'slogan' => 'Store Owner',
                'celphone' => '000-000-0001',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'role' => 'store',
            ]
        );

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
