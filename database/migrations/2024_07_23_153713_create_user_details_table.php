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
        Schema::create('user_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->boolean('locked')->default(0);
            $table->foreignId('group_id')->nullable();
            $table->boolean('marketplace_access')->default(0);
            $table->string('mac_address')->nullable();
            $table->string('sim_card')->nullable();
            $table->string('sn')->nullable();
            $table->string('requesr_per_second')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_details');
    }
};
