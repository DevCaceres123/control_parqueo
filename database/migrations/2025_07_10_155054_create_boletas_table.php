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
       Schema::create('boletas', function (Blueprint $table) {
        $table->id('id_boleta');
        $table->unsignedBigInteger('vehiculo_id');
        $table->foreign('vehiculo_id')->references('id_vehiculo')->on('vehiculos')->onDelete('cascade');

        $table->unsignedBigInteger('usuario_id');
        $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

        $table->unsignedBigInteger('cliente_id');
        $table->foreign('cliente_id')->references('id_cliente')->on('clientes')->onDelete('cascade');

        $table->dateTime('fecha_emision');
        $table->decimal('total_pago', 10, 2);
        $table->decimal('multa', 10, 2)->nullable();
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boletas');
    }
};
