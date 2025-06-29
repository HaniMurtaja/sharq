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
        Schema::create('exported_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_id')->nullable();
            $table->string('order_number')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('value')->nullable();
            $table->string('service_fees')->nullable();
            $table->string('total')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_type')->nullable();
            $table->unsignedInteger('driver_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('client_account')->nullable();
            $table->unsignedInteger('ingr_shop_id')->nullable();
            $table->string('shop_name')->nullable();
            $table->unsignedInteger('ingr_branch_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('city')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->dateTime('order_created_at')->nullable();
            $table->dateTime('driver_assigned_at')->nullable();
            $table->dateTime('arrived_to_pickup_time')->nullable();
            $table->dateTime('picked_up_time')->nullable();
            $table->dateTime('arrived_to_dropoff_time')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->string('pickup_distance')->nullable();
            $table->string('delivery_distance')->nullable();
            $table->string('pickup_duration')->nullable();
            $table->string('delivery_duration')->nullable();
            $table->unsignedInteger('status_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->timestamps();
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('exported_order_id')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exported_orders');
    }
};
