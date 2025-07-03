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
        Schema::create('client_invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->date('invoice_date');
            $table->date('due_date');

            $table->enum('status', ['generated_under_review', 'confirmed_sent_unpaid', 'paid'])->default('generated_under_review');

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('SAR');
            
            $table->text('notes')->nullable();
            $table->json('zatca_qr_data')->nullable(); 
            $table->string('qr_code_path')->nullable();
            $table->json('client_emails')->nullable(); 

            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_invoices');
    }
};
