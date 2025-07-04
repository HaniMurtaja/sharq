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
        Schema::table('client_branches', function (Blueprint $table) {
            $table->boolean('status')->default(0);
            $table->boolean('auto_dispatch')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_branches', function (Blueprint $table) {
            //
        });
    }
};
