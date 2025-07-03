<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_drivers', function (Blueprint $table) {
            $table->json('operator_details')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_drivers', function (Blueprint $table) {
            //
        });
    }
};
