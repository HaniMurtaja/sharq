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
        Schema::table('client_invoices', function (Blueprint $table) {

            if (!Schema::hasColumn('client_invoices', 'pdf_path')) {
                $table->string('pdf_path')->nullable()->after('zatca_qr_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('client_invoices', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
};
