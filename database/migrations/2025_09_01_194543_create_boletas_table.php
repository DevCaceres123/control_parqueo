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
            $table->id();
            $table->string('num_boleta')->unique();
            $table->string('placa')->nullable();
            $table->string('persona')->nullable();
            $table->datetime('entrada');
            $table->datetime('salida');
            $table->time('retraso');            
            $table->enum('estado_parqueo',['ingreso','salida']);
            $table->enum('estado_impresion',['generado','impreso']);            
            $table->integer('total')->nullable();
            
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('usuario_id');


            $table->foreign('vehiculo_id')
                ->references('id')
                ->on('vehiculos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            
            $table->foreign('usuario_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            
            

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
