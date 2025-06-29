<?php

namespace Database\Seeders;

use App\Models\ClientBranches;
use Illuminate\Database\Seeder;
use App\Repositories\FirebaseRepositoryTest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FirebaseBranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $branches = ClientBranches::get();

        $firebase = new FirebaseRepositoryTest();
        $firebase->saveBranches($branches);

        $this->command->info('Branches uploaded to Firebase.');
    }
}
