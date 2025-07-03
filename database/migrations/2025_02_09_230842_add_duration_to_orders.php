<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('pickup_duration')->nullable()->default(0);
            $table->integer('delivery_duration')->nullable()->default(0);
            $table->integer('pickup_distance')->nullable()->default(0);
            $table->integer('delivery_distance')->nullable()->default(0);
            $table->dateTime('driver_accepted_time')->nullable();
            $table->dateTime('arrived_to_pickup_time')->nullable();
            $table->dateTime('picked_up_time')->nullable();
            $table->dateTime('arrived_to_dropoff_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
