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
        Schema::create('company_financial_settings', function (Blueprint $table) {
            $table->id();

            $table->string('company_name');

            $table->string('tax_id')->nullable();
            $table->string('commercial_registration')->nullable();
            
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('iban')->nullable();

            $table->json('additional_fields')->nullable(); 
            $table->integer('payment_due_days')->default(3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_financial_settings');
    }
};
