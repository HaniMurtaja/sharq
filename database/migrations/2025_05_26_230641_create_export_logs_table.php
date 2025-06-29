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
        Schema::create('export_logs', function (Blueprint $table) {
            $table->id();
              $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('file_name');
    $table->string('file_path');
    $table->boolean('is_ready')->default(false); // ⬅️ ما يكونش ظاهر إلا لما يكون جاهز
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('export_logs');
    }
};
