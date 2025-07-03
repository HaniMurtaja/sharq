<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('client_branches', function (Blueprint $table) {
            $table->integer('pickup_id')->nullable()->default(1);
            $table->bigInteger('custom_id')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('client_branches', function (Blueprint $table) {
            //
        });
    }
};
