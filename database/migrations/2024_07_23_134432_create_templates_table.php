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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->json('system_billing')->nullable();
            $table->json('clients')->nullable();
            $table->json('dashboard')->nullable();
            $table->json('dispatcher')->nullable();
            $table->json('customers')->nullable();
            $table->json('users')->nullable();
            $table->json('reports')->nullable();
            $table->json('drivers')->nullable();
            $table->json('driver')->nullable();
            $table->json('operator_billings')->nullable();
            $table->json('orders')->nullable();
            $table->json('road_assistance')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates')->nullable();
    }
};
