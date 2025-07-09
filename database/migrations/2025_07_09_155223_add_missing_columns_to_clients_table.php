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
        Schema::table('clients', function (Blueprint $table) {

            if (!Schema::hasColumn('clients', 'currency')) {

                $table->string('currency', 3)->default('SAR')->after('payment_terms');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
           if (Schema::hasColumn('clients', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
