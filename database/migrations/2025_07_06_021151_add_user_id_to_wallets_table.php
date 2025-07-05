<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
           
            if (!Schema::hasColumn('wallets', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id');
            }

            if (Schema::hasColumn('wallets', 'operator_id')) {
                $table->foreignId('operator_id')->nullable()->change();
            }
            
            if (!Schema::hasColumn('wallets', 'currency')) {
                $table->string('currency', 3)->default('SAR')->after('balance');
            }

            $table->index(['user_id']);
            $table->index(['operator_id']);
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasColumn('wallets', 'user_id')) {
                $table->dropIndex(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('wallets', 'currency')) {
                $table->dropColumn('currency');
            }
            
            $table->dropIndex(['operator_id']);
        });
    }
};
