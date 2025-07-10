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
        Schema::create('resumen_diario', function (Blueprint $table) {
        $table->id('id_resumen');
        $table->date('fecha');
        $table->integer('total_vehiculos');
        $table->decimal('total_ingresos', 10, 2);
        $table->decimal('total_multas', 10, 2);
        $table->foreignId('creado_por')->constrained('users')->onDelete('cascade');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resumen_diario');
    }
};
