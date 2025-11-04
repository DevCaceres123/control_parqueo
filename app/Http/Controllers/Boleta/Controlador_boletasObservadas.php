<?php

namespace App\Http\Controllers\Boleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleta;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class Controlador_boletasObservadas extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('control.boletas_observadas.inicio')) {
            return redirect()->route('inicio');
        }

        return view("administrador.boletas.listarBoletasObservadas");
    }
    public function listarBoletasObservadas(Request $request)
    {
        $hoy = Carbon::now();
        $fechaLimite = $hoy->copy()->subDays(30);

        // Query base: boletas > 30 días
        $query = Boleta::with([
                'vehiculo' => function ($q) {
                    $q->select(['id', 'nombre']);
                },
                'contacto' => function ($q) {
                    $q->select(['id', 'telefono']);
                },
            ])
            ->select('id', 'placa', 'ci', 'entrada_veh', 'vehiculo_id', 'contacto_id')
            ->whereDate('entrada_veh', '<', $fechaLimite)
            ->orderBy('id', 'desc');

        // Búsqueda
        if (!empty($request->search['value'])) {
            $search = $request->search['value'];
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%");
            });
        }

        // Total de registros antes de paginación
        $recordsTotal = $query->count();

        // Paginación
        $boletas = $query->skip($request->start)->take($request->length)->get();

        // Mapear y calcular días de estadía
        $datos_registros = $boletas->map(function ($boleta) use ($hoy) {
            $entrada = Carbon::parse($boleta->entrada_veh);

            return [
                'id' => $boleta->id,
                'placa' => $boleta->placa,
                'ci' => $boleta->ci,
                'contacto' => $boleta->contacto ? $boleta->contacto->telefono : null,
                'vehiculo' => $boleta->vehiculo ? [
                    'id' => $boleta->vehiculo->id,
                    'nombre' => $boleta->vehiculo->nombre,
                ] : null,
                'entrada_veh' => $entrada->locale('es')
                        ->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i:s'),
               'dias_estadia' => (int) $entrada->diffInDays(Carbon::now()), // fuerza entero
            ];
        });

        // Respuesta JSON para DataTables
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $datos_registros,
            'permissions' => [
                'contacto' => auth()->user()->can('control.boletas_observadas.contacto'),
                'entrada' => auth()->user()->can('control.boletas_observadas.ticket_entrada'),                
            ],
        ]);
    }





    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
