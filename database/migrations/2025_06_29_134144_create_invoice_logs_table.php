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
        Schema::create('invoice_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')->constrained('client_invoices')->onDelete('cascade');
            $table->string('action'); 
            $table->foreignId('user_id')->nullable()->constrained('users'); 

            $table->json('old_data')->nullable(); 
            $table->json('new_data')->nullable(); 

            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_logs');
    }
};
