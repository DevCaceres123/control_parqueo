<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tarifas;
use Exception;
use Illuminate\Support\Facades\DB;

class Controlador_tarifas extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("administrador.configuracion.tarifas");
    }


    public function listarTarifas(Request $request){

        $query = Tarifas::select('id', 'nombre', 'precio','estado')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('precio', 'like', '%' . $request->search['value'] . '%')                
                ;
            });
        }

        // Total de registros antes del filtrado
        $recordsTotal = $query->count();

        // Paginación y orden
        $sedes = $query->skip($request->start)->take($request->length)->get();

        // Respuesta
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal, // Ajustar si hay filtros
            'data' => $sedes,
            'permisos' => [
                'editar' => auth()->user()->can('config.vehiculos.editar'),
                'eliminar' => auth()->user()->can('config.vehiculos.eliminar'),
                'estado' => auth()->user()->can('config.vehiculos.desactivar'),
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
        DB::beginTransaction();
        try {
            
            $tarifa = Tarifas::find($id);
            if (!$tarifa) {
                throw new Exception('tarifa no encontrado');
            }

            $tarifa->delete();
            DB::commit();
        
            $this->mensaje("exito", "tarifa eliminada correctamente");
            return response()->json($this->mensaje, 200);
        
        } catch (Exception $e) {
            DB::rollBack();
        
            $this->mensaje("error", "error" . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }

    // Mensaje para mostrar al usuario
    public function mensaje($titulo, $mensaje)
    {
        $this->mensaje = [
            'tipo' => $titulo,
            'mensaje' => $mensaje,
        ];
    }
}
