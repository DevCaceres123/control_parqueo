<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colores = [
            ['nombre' => 'Blanco', 'color' => '#FFFFFF'],
            ['nombre' => 'Negro', 'color' => '#000000'],
            ['nombre' => 'Gris', 'color' => '#808080'],
            ['nombre' => 'Plateado', 'color' => '#C0C0C0'],
            ['nombre' => 'Rojo', 'color' => '#FF0000'],
            ['nombre' => 'Azul', 'color' => '#0000FF'],
            ['nombre' => 'Verde', 'color' => '#008000'],
            ['nombre' => 'Amarillo', 'color' => '#FFFF00'],
            ['nombre' => 'Naranja', 'color' => '#FFA500'],
            ['nombre' => 'Marrón', 'color' => '#8B4513'],
            ['nombre' => 'Bordó', 'color' => '#800000'],
            ['nombre' => 'Celeste', 'color' => '#87CEEB'],
            ['nombre' => 'Beige', 'color' => '#F5F5DC'],
            ['nombre' => 'Vino', 'color' => '#722F37'],
            ['nombre' => 'Azul Marino', 'color' => '#000080'],
            ['nombre' => 'Verde Oliva', 'color' => '#556B2F'],
            ['nombre' => 'Dorado', 'color' => '#FFD700'],
            ['nombre' => 'Gris Oscuro', 'color' => '#505050'],
            ['nombre' => 'Rosado', 'color' => '#FFC0CB'],
        ];

        DB::table('colores')->insert($colores);
    }
}
