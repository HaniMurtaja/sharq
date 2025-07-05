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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('client_invoices')->onDelete('cascade');
            $table->string('receipt_number')->unique()->nullable();
            $table->decimal('amount_paid', 10, 2);
            $table->date('payment_date');
            $table->enum('payment_method', ['bank_transfer', 'cash', 'tap_gateway', 'other']);
            $table->string('transaction_reference')->nullable();
            $table->enum('status', ['under_review', 'confirmed'])->default('under_review');
            $table->text('notes')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};
