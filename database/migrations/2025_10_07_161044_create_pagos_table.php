<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_id');
            $table->unsignedBigInteger('accesor_id')->nullable();
            $table->date('fecha_pago')->nullable();          // fecha en la que se pagó
            $table->date('fecha_a_pagar');                   // fecha programada
            $table->decimal('monto', 8, 2);
            $table->enum('estado', ['PENDIENTE', 'PAGADO', 'NO PAGO', 'PAGO ATRASADO',])->default('PENDIENTE');
            $table->string('observacion')->nullable();
            $table->timestamps();

            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
            $table->foreign('accesor_id')->references('id')->on('accesors')->onDelete('set null');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
