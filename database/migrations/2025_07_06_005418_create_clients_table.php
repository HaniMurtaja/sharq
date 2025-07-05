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
            $table->string('account_number')->unique();
            $table->string('company_name')->nullable();
            $table->json('billing_emails')->nullable();
            $table->boolean('auto_generate_invoice')->default(false);
            $table->text('invoice_template_notes')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('currency', 3)->default('SAR');
            $table->timestamp('last_invoice_date')->nullable();
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
