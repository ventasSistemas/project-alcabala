<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id');
            $table->unsignedBigInteger('puesto_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('frecuencia_pago', ['SEMANAL', 'MENSUAL', 'ANUAL'])->default('SEMANAL');
            $table->decimal('monto', 8, 2);
            $table->boolean('renovable')->default(true);
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onDelete('cascade');
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
