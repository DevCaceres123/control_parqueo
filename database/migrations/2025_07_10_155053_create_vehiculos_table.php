<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
        $table->id('id_vehiculo');
        $table->string('placa', 20)->unique();
        $table->unsignedBigInteger('tipo_vehiculo_id');
        $table->foreign('tipo_vehiculo_id')->references('id_tipo')->on('tipo_vehiculo')->onDelete('cascade');
        $table->dateTime('hora_entrada');
        $table->dateTime('hora_salida')->nullable();
        $table->enum('estado', ['dentro', 'fuera']);
        $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos'); // elimina la tabla
    }
};
