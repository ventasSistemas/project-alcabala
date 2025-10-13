<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombres' => $this->faker->name(),
            'dni' => $this->faker->unique()->numerify('########'), // 8 dígitos
            'celular' => $this->faker->optional()->numerify('9########'),
        ];
    }
}