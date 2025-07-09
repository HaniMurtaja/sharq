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
        Schema::table('orders', function (Blueprint $table) {
            
            if (Schema::hasColumn('orders', 'ingr_shop_id')) {
                $table->index(['ingr_shop_id', 'created_at', 'invoiced']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['ingr_shop_id', 'created_at', 'invoiced']);
            });
        });
    }
};
