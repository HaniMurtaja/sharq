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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('operator_id'); // Foreign key to operators table
            $table->decimal('balance', 15, 2)->default(0); // Balance with two decimal places
            $table->string('currency', 3)->nullable(); // Currency code (e.g., USD, EUR)
            $table->timestamps(); // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
