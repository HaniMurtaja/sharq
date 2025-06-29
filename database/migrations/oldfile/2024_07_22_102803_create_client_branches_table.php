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
        Schema::create('client_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->string('name');
            $table->string('phone');
            $table->foreignId('client_group')->nullable();
            $table->foreignId('driver_group')->nullable();
            $table->json("map_location")->nullable();
            $table->string('country')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->foreignId('area_id')->nullable();
            $table->string('street');
            $table->string('landmark')->nullable();
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment')->nullable();
            $table->longText('description')->nullable();
            $table->json('business_hours')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_branches');
    }
};
