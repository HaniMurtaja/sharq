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
        Schema::dropIfExists('orders');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_lat')->nullable();
            $table->string('pickup_lng')->nullable();
            $table->foreignId('pickup_id')->nullable();
            $table->foreignId('client_order_id')->nullable();
        
            $table->decimal('value')->nullable();
            $table->integer('payment_type')->nullable();
            $table->integer('preparation_time')->default(0);
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('city')->nullable();
            $table->string('customer_phone');
            $table->string('customer_name')->nullable();
            $table->dateTime('deliver_at')->nullable();
            $table->longText('details')->nullable();
            $table->boolean('pickup_poa')->nullable();
            $table->boolean('dropoff_poa')->nullable();
            $table->foreignId('ingr_shop_id')->nullable();
            $table->foreignId('ingr_branch_id')->nullable();
            $table->string('ingr_shop_name')->nullable();
            $table->string('ingr_branch_name')->nullable();
            $table->string('ingr_branch_lat')->nullable();
            $table->string('ingr_branch_lng')->nullable();
            $table->string('ingr_branch_phone')->nullable();
            $table->longText('instruction')->nullable();
            $table->longText('pickup_instruction')->nullable();
            $table->string('proof_of_action')->nullable();
            $table->float('items_no')->nullable();
            $table->string('vehicle')->nullable();
            $table->integer('driver_arrive_time')->default(1);
            $table->float('service_fees')->default(0);
            $table->integer('delivered_in')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
