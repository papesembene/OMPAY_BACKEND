<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Marchant;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
        $users = User::factory(3)->create();

       
        $merchants = Marchant::factory(2)->create();

        
        Transaction::factory(4)
            ->recycle($users)      
            ->recycle($merchants)  
            ->create();
    }
}