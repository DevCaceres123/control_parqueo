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
        Schema::create('historial_registros', function (Blueprint $table) {
            $table->id();
            $table->string('cod_qr',255);
            $table->string('puesto',200);
            $table->unsignedBigInteger('id_usuario');
            $table->string('nombre usuario',200);
            $table->double('precio',10,2);

            $table->timestamps();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_registros');
    }
};
