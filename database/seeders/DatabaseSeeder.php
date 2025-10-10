<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Accesor;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            UserSeeder::class,
        ]);

        Accesor::factory(10)->create();
    }
}
