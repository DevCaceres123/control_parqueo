<?php

namespace Database\Factories;

use App\Models\Boleta;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Helpers\HashidsHelper;

class BoletaFactory extends Factory
{
    protected $model = Boleta::class;

    public function definition(): array
    {
        // Seleccionar un vehículo existente
        $vehiculo = Vehiculo::inRandomOrder()->first();

        // Si no hay vehículos, creamos uno temporal
        if (!$vehiculo) {
            $vehiculo = Vehiculo::factory()->create();
        }

        // Fecha de entrada aleatoria dentro de los últimos 7 días
        $entrada = Carbon::now()->subDays(rand(0, 15))->setTime(rand(6, 20), rand(0, 59));

        // Fecha de salida máxima (al siguiente día a las 15:00)
        $salidaMax = $entrada->copy()->addDay()->setTime(15, 0);

        // Crear el JSON del reporte (sin codigoUnico todavía)
        $reporte_json = [
            "usuario" => [
                "nombres" => "ADMIN",
                "apellidos" => "ADMIN ADMIN"
            ],
            "tarifa_vehiculo" => [
                "tarifa" => $vehiculo->tarifa ?? 25,
                "nombre" => $vehiculo->nombre ?? "vehículo"
            ],
            "fecha_generada" => $entrada->format('Y-m-d H:i:s'),
            "fecha_finalizacion" => $salidaMax->format('Y-m-d H:i:s'),
            "placa" => $this->faker->regexify('[A-Z0-9]{6}'),
            "nombre" => null,
            "ci" => null,
            "codigoUnico" => null // se llenará después
        ];

        // Crear la boleta (sin num_boleta todavía)
        return [
            'placa' => $reporte_json['placa'],
            'entrada_veh' => $entrada,
            'salidaMax' => $salidaMax,
            'estado_parqueo' => 'ingreso',
            'reporte_json' => json_encode($reporte_json),
            'vehiculo_id' => $vehiculo->id,
            'usuario_id' => 1,
            'estado_impresion' => 'generado',
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Boleta $boleta) {
            // Generar num_boleta encriptando el ID
            $hashids = new HashidsHelper();
            $num_boleta = $hashids->encode($boleta->id);

            // Actualizar num_boleta
            $boleta->num_boleta = $num_boleta;

            // Actualizar el reporte_json para incluir el mismo código único
            $reporte = json_decode($boleta->reporte_json, true);
            $reporte['codigoUnico'] = $num_boleta;
            $boleta->reporte_json = json_encode($reporte);

            $boleta->save();
        });
    }
}
