<?php

namespace App\Http\Controllers\Boleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Vehiculo;
use App\Models\Config_atraso;
use App\Models\User;
use Carbon\Carbon;

class Controlador_listarBoletas extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if (!auth()->user()->can('control.generar.inicio')) {
        //     return redirect()->route('inicio');
        // }

        $encargados_puesto = User::select('id', 'nombres', 'apellidos')
        
        ->where('estado', 'activo')
        ->get();




        return view("administrador.boletas.listar", compact('encargados_puesto'));
    }




    public function listarTodasBoletas(Request $request)
    {

        $fecha_actual = $request->input('fecha') ? Carbon::parse($request->input('fecha'))->toDateString() : null;
        $encargado = $request->input('encargado') ?? null;


        $query = Boleta::with([
            'vehiculo' => function ($query) {
                $query->select(['id', 'nombre', 'tarifa']);
            },
        ])->select('id', 'placa', 'ci', 'entrada_veh', 'salida_veh', 'estado_parqueo', 'total', 'vehiculo_id','retraso')->orderBy('id', 'desc');



        if ($fecha_actual) {
            $query->whereDate('entrada_veh', $fecha_actual);

            if ($encargado !== null) {
                $query->where('usuario_id', $encargado);
            } else {
                $query->where('usuario_id', auth()->id());
            }
        }





        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('placa', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('num_boleta', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('ci', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('estado_parqueo', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('total', 'like', '%' . $request->search['value'] . '%')
                ->orWhereHas('vehiculo', function ($vehiculoQuery) use ($request) {
                    $vehiculoQuery->where('nombre', 'like', '%' . $request->search['value'] . '%');
                });
            });
        }


        // Total de registros antes del filtrado
        $recordsTotal = $query->count();

        // Paginación y orden
        $datos_registros = $query->skip($request->start)->take($request->length)->get();


        $datos_registros = $query->get()->map(function ($boleta) {
            return [
                'id' => $boleta->id,
                'placa' => $boleta->placa,
                'ci' => $boleta->ci,
                'estado_parqueo' => $boleta->estado_parqueo,
                'total' => $boleta->total,
                'vehiculo' => $boleta->vehiculo ? [
                    'id' => $boleta->vehiculo->id,
                    'nombre' => $boleta->vehiculo->nombre,
                    'tarifa' => $boleta->vehiculo->tarifa,
                ] : null,
                'entrada_veh' => $boleta->entrada_veh
                    ? Carbon::parse($boleta->entrada_veh)
                        ->locale('es')
                        ->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i:s')
                    : null,
                'salida_veh' => $boleta->salida_veh
                    ? Carbon::parse($boleta->salida_veh)
                        ->locale('es')
                        ->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i:s')
                    : null,
            ];
        });
        // Respuesta
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal, // Ajustar si hay filtros
            'data' => $datos_registros,
            'permissions' => [
                'editar' => auth()->user()->can('afiliado.editar'),
                'eliminar' => true,
                'estado' => auth()->user()->can('afiliado.estado'),
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
