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

            $table->string('numero_pago')->nullable();     // numero de pago
            $table->date('fecha_pago')->nullable();          // fecha en la que se pagó
            $table->date('fecha_a_pagar');                   // fecha programada
            $table->decimal('monto', 8, 2);  // monto
            $table->enum('estado', ['PAGADO', 'PAGO ATRASADO',]);  //estado del registro
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }

        //$table->unsignedBigInteger('contrato_id');
        //$table->unsignedBigInteger('accesor_id')->nullable();
        //$table->string('observacion')->nullable();
        
        //$table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        //$table->foreign('accesor_id')->references('id')->on('accesors')->onDelete('set null');
};
