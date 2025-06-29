<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->string('app_version')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            //
        });
    }
};
