<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table): void {
            $table->string('type_of_sell')->nullable()->after('price');
        });

        Schema::table('old_cart_items', function (Blueprint $table): void {
            $table->string('type_of_sell')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('old_cart_items', function (Blueprint $table): void {
            $table->dropColumn('type_of_sell');
        });

        Schema::table('cart_items', function (Blueprint $table): void {
            $table->dropColumn('type_of_sell');
        });
    }
};
