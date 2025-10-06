<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vehiculo;

class vehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehiculo = new Vehiculo();
        $vehiculo->nombre = 'taxi';
        $vehiculo->descripcion = '2 ejes';
        $vehiculo->estado = "activo";
        $vehiculo->tarifa = 15;
        $vehiculo->usuario_id = 1;

        $vehiculo->save();


        $vehiculo2 = new Vehiculo();
        $vehiculo2->nombre = 'bagoneta';
        $vehiculo2->descripcion = '4 ejes';
        $vehiculo2->estado = "activo";
        $vehiculo2->tarifa = 25;
        $vehiculo2->usuario_id = 1;
        $vehiculo2->save();


        $vehiculo3 = new Vehiculo();
        $vehiculo3->nombre = 'moto';
        $vehiculo3->descripcion = '1ejes';
        $vehiculo3->estado = "activo";
        $vehiculo3->tarifa = 10;
        $vehiculo3->usuario_id = 1;
        $vehiculo3->save();



    }
}
