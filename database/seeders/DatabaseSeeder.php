<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Accesor;
use App\Models\CategoriaEstablecimiento;
use App\Models\Cliente;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
        CategoriaEstablecimiento::factory(5)->create();
        Cliente::factory(5)->create();
    }
}
