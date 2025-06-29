<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        for ($i = 1; $i <= 10; $i++) {
            DB::table('orders')->insert([
                'client_order_id_string' => 'TEST-' . Str::padLeft($i, 5, '0'),
                'value' => rand(10, 500),
                'payment_type' => 1,
                'lat' => '30.0444',
                'lng' => '31.2357',
                'city' => 1,
                'customer_phone' => '01000000' . rand(100, 999),
                'customer_name' => 'Test Customer ' . $i,
                'ingr_shop_id' => 2633,
                'ingr_branch_id' => 9999925066,
                'status' => 2,
                'service_fees' => rand(5, 30),
                'created_at' => $now->copy()->subMinutes(rand(0, 720)),
                'updated_at' => $now,
            ]);
        }

    }
}
