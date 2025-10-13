<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriaEstablecimiento>
 */
class CategoriaEstablecimientoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->company(),
            'direccion' => $this->faker->address(),
            'imagen_lugar' => null, // puedes cambiarlo a un path de imagen si deseas
            'longitud_actual' => $this->faker->longitude(-75, -72), // Ejemplo: coordenadas en Perú
            'latitud_actual' => $this->faker->latitude(-13, -11),
            'longitud_destino' => $this->faker->longitude(-75, -72),
            'latitud_destino' => $this->faker->latitude(-13, -11),
            'hora_apertura' => $this->faker->time('H:i:s', '08:00:00'),
            'hora_cierre' => $this->faker->time('H:i:s', '20:00:00'),
            'pago_puesto' => $this->faker->randomFloat(2, 10, 200),
            'pago_inscripcion_anual' => $this->faker->randomFloat(2, 10, 200),
        ];
    }
}