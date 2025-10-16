<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('accesor_id')->nullable()->constrained('accesors')->onDelete('cascade');
            $table->decimal('monto_inicial', 10, 2)->default(0);
            $table->decimal('total_ingresos', 10, 2)->default(0);
            $table->decimal('total_egresos', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->default(0);
            $table->enum('estado', ['ABIERTA', 'CERRADA'])->default('ABIERTA');
            $table->date('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
