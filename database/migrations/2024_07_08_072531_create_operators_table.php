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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id');
            $table->date('birth_date');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->string('social_id_no');
            $table->string('city');
            $table->string('iban');
            $table->foreignId('group_id');
            $table->foreignId('branch_group_id');
            $table->foreignId('shift_id');
            $table->json('days_off')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
