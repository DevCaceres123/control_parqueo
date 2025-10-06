<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Config_atraso;

class ConfAtrasoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conf_atraso = new Config_atraso();
        $conf_atraso->tiempo_extra = '01:00:00';
        $conf_atraso->estado = 'activo';
        $conf_atraso->monto = 20;
        $conf_atraso->save();

    }
}
