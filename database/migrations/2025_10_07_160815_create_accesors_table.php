<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesors', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('direccion')->nullable();
            $table->string('celular', 9)->nullable();
            $table->string('dni', 8)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesors');
    }
};
