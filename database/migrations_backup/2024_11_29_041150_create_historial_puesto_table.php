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
        Schema::create('historial_puesto', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion_edicion',100)->nullable();
            $table->unsignedBigInteger('puesto_id');
            $table->unsignedBigInteger('usuario_id');
            
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('restrict');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_puesto');
    }
};
