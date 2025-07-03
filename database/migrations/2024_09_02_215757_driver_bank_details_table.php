<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('driver_bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('iban')->nullable();
            $table->string('bank_name')->nullable();
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('driver_bank_details', function (Blueprint $table) {
            // Dropping the primary key column
            $table->dropPrimary('id');

            // Dropping new columns
            $table->dropColumn(['iban', 'bank_name']);

            // Reverting foreign key column to nullable
            $table->foreignId('operator_id')
                  ->nullable()
                  ->constrained('operators')
                  ->change();
            
            // Dropping timestamps
            $table->dropTimestamps();
        });
    }
};
