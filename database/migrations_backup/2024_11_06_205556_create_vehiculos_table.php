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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 25);
            $table->string('descripcion');
            $table->unsignedBigInteger('persona_id');
            $table->unsignedBigInteger('color_id');
            $table->unsignedBigInteger('tipovehiculo_id');
            $table->timestamps();

            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('restrict');
            $table->foreign('color_id')->references('id')->on('colores')->onDelete('restrict');
            $table->foreign('tipovehiculo_id')->references('id')->on('tipo_vehiculos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
