<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('colores', function (Blueprint $table) {
            $table->id(); // id autoincremental
            $table->string('nombre', 100); // Ej: Rojo metálico, Azul cielo
            $table->string('color', 50); // nombre del color
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps(); // opcional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colores');
    }
};
