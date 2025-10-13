<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config_atraso;
use Illuminate\Support\Facades\DB;
use Exception;

class Controlador_config_atraso extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('config.atraso.inicio')) {
            return redirect()->route('inicio');
        }

        return view("administrador.configuracion.conf_atraso");
    }

    /**
     * Show the form for creating a new resource.
     */


    public function listarConfAtraso(Request $request){
        

        $query = Config_atraso::select(
            'id',
            DB::raw("
                    CONCAT(
                    IF(HOUR(tiempo_extra) > 0, CONCAT(HOUR(tiempo_extra),' horas '), ''),
                    IF(MINUTE(tiempo_extra) > 0, CONCAT(MINUTE(tiempo_extra),' min'), '')) as tiempo_extra"),
                'monto',
                'estado'
                )->orderBy('id','desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('tiempo_extra', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('monto', 'like', '%' . $request->search['value'] . '%');
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
                'editar' => auth()->user()->can('config.atraso.editar'),                
                'estado' => auth()->user()->can('config.atraso.desactivar'),
            ],
        ]);
    }
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
         $atraso = Config_atraso::Find($id);
        if (!$atraso) {
            $this->mensaje('error', 'Atraso no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $atraso);

        return response()->json($this->mensaje, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
                  
            $atraso = Config_atraso::Find($request->atraso_id);

            if (!$atraso) {
                throw new Exception('atraso no encontrado');
            }
            $atraso->tiempo_extra=$request->tiempo_extra;
            $atraso->monto=$request->monto;
            $atraso->save();
            DB::commit();

            $this->mensaje("exito", "Configuracion de atrasos editado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function cambiarEstadoConfig(String $id_configuracion,request $request){
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $atraso =Config_atraso::Find($id_configuracion);

            if (!$atraso) {
                throw new Exception('atraso no encontrado');
            }
            if ($request->estado == "activo") {
                $atraso->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $atraso->estado = "activo";
            }

            $atraso->save();          
            DB::commit();

            $this->mensaje("exito", "Estado combiado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
