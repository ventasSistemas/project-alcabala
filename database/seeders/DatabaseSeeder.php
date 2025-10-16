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

        Accesor::factory(3)->create();
        CategoriaEstablecimiento::factory(3)->create();
        Cliente::factory(1)->create();
    }
}
