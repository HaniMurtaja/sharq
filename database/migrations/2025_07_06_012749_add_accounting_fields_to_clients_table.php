<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
           
            $table->bigInteger('user_id')->nullable()->change();
            
         
            if (!Schema::hasColumn('clients', 'account_number')) {
                $table->string('account_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('clients', 'company_name')) {
                $table->string('company_name')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('clients', 'billing_emails')) {
                $table->json('billing_emails')->nullable()->after('company_name');
            }
            if (!Schema::hasColumn('clients', 'auto_generate_invoice')) {
                $table->boolean('auto_generate_invoice')->default(false)->after('billing_emails');
            }
            if (!Schema::hasColumn('clients', 'invoice_template_notes')) {
                $table->text('invoice_template_notes')->nullable()->after('auto_generate_invoice');
            }
            if (!Schema::hasColumn('clients', 'payment_terms')) {
                $table->string('payment_terms')->nullable()->after('invoice_template_notes');
            }
            if (!Schema::hasColumn('clients', 'last_invoice_date')) {
                $table->timestamp('last_invoice_date')->nullable()->after('payment_terms');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
          
            $table->bigInteger('user_id')->nullable(false)->change();
            
           
            $columns = [
                'account_number',
                'company_name', 
                'billing_emails',
                'auto_generate_invoice',
                'invoice_template_notes',
                'payment_terms',
                'last_invoice_date'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
