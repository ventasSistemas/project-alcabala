<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_establecimientos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('imagen_lugar')->nullable(); // Imagen del lugar (opcional)

            // Coordenadas actuales (capturadas automáticamente)
            $table->decimal('longitud_actual', 10, 7)->nullable();
            $table->decimal('latitud_actual', 10, 7)->nullable();

            // Coordenadas destino (ingresadas manualmente)
            $table->decimal('longitud_destino', 10, 7)->nullable();
            $table->decimal('latitud_destino', 10, 7)->nullable();

            // Campos horarios
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierre')->nullable();

            // Pago del puesto
            $table->decimal('pago_puesto', 8, 2)->default(0);

            $table->timestamps();
        });

        // Tabla pivote para asignar varios personales (Accesores)
        Schema::create('accesor_categoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_establecimiento_id')
                  ->constrained('categoria_establecimientos')
                  ->onDelete('cascade');

            $table->foreignId('accesor_id')
                  ->constrained('accesors')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesor_categoria');
        Schema::dropIfExists('categoria_establecimientos');
    }
};
