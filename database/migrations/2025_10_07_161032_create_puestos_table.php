<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categoria_establecimientos')->onDelete('cascade');
            $table->string('numero_puesto')->unique();
            $table->boolean('disponible')->default(true);

            $table->string('imagen_puesto')->nullable();
            $table->json('servicios')->nullable();
            $table->text('observaciones')->nullable();

            // 🔹 Nuevos campos
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->time('hora_apertura')->nullable();
            $table->time('hora_cierre')->nullable();
            $table->date('primer_pago_fecha')->nullable();
            $table->decimal('primer_pago_monto', 8, 2)->nullable();
            $table->enum('modo_pago', ['SEMANAL', 'MENSUAL', 'ANUAL'])->nullable();
            $table->string('accesor_cobro')->nullable(); // o ->foreignId('accesor_id') si se maneja en tabla aparte

            $table->timestamps();

            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puestos');
    }
};
