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
        Schema::create('tipo_vehiculo', function (Blueprint $table) {
        $table->id('id_tipo');
        $table->string('nombre', 50);
        $table->decimal('tarifa_hora', 10, 2);
        $table->decimal('tarifa_dia', 10, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_vehiculo');
    }
};
