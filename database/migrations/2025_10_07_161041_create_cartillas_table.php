<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartillas', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('puesto_id')->constrained('puestos')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();

            // Información de la cartilla
            $table->integer('nro')->nullable(); 
            $table->date('fecha_pagar');
            $table->decimal('cuota', 8, 2);
            $table->string('observacion')->default('Pendiente'); 

            // Datos de control
            $table->string('modo_pago')->nullable(); 
            $table->string('accesor_cobro')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartillas');
    }
};
