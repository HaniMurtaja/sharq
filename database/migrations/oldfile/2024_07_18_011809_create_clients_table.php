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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('country')->nullable();
            $table->foreignId('city_id');
            $table->string('currency')->nullable();
            $table->string('default_prepartion_time');
            $table->string('min_prepartion_time');
            $table->string('partial_pay')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('client_group_id')->nullable();
            $table->foreignId('driver_group_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
