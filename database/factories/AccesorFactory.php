<?php

namespace Database\Factories;
use App\Models\Accesor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccesorFactory extends Factory
{
    protected $model = Accesor::class;
    public function definition()
    {
        return [
            'nombres' => $this->faker->name(),
            'direccion' => $this->faker->address(),
            'celular' => $this->faker->optional()->numerify('9########'),
            'dni' => $this->faker->unique()->numerify('########'),
        ];
    }
}
