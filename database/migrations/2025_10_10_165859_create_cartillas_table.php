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

            // 🔗 Relaciones
            $table->foreignId('puesto_id')->constrained('puestos')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();

            // 📅 Información de la cartilla
            $table->integer('nro')->nullable(); // Nro de pago
            $table->date('fecha_pagar');
            $table->decimal('cuota', 8, 2);
            $table->string('observacion')->default('Pendiente'); // Pendiente o Pagado

            // 🧾 Datos de control
            $table->string('modo_pago')->nullable(); // SEMANAL, MENSUAL o ANUAL
            $table->string('accesor_cobro')->nullable(); // Persona encargada

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartillas');
    }
};
