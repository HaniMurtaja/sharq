<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Operator;
use App\Models\OperatorDetail;

class OperatorCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $operators = OperatorDetail::all();

        foreach ($operators as $operator) {
            DB::table('operator_cities')->insert([
                'operator_id' => $operator->operator_id, 
                'city_id' => $operator->city_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
