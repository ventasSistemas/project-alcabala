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
            $table->string('numero_pago')->nullable();    
            $table->date('fecha_pago')->nullable();        
            $table->date('fecha_a_pagar');                  
            $table->decimal('monto', 8, 2);  
            $table->enum('estado', ['PAGADO', 'PAGO ATRASADO']); 
            $table->boolean('enviado_a_caja')->default(false);
            $table->foreignId('cartilla_id')->nullable()->constrained('cartillas')->onDelete('set null');
            $table->foreignId('accesor_id')->nullable()->constrained('accesors')->onDelete('set null');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
