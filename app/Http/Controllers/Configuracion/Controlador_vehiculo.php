<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehiculo;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Vehiculo\VehiculoRequest;
class Controlador_vehiculo extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("administrador.configuracion.vehiculos");
    }



    public function listarVehiculos(Request $request)
    {
        $query = Vehiculo::select('id', 'nombre', 'descripcion', 'estado', 'tarifa')->orderBy('id', 'desc');

        if (!empty($request->search['value'])) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('estado', 'like', '%' . $request->search['value'] . '%')
                ->orWhere('tarifa', 'like', '%' . $request->search['value'] . '%')
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
    public function store(VehiculoRequest $request)
    {
        
        DB::beginTransaction();

        try {
            // Guardar la sede
            $vehiculo = new Vehiculo();
            $vehiculo->nombre = $request->nombre;
            $vehiculo->descripcion = $request->descripcion_vehiculo;
            $vehiculo->estado = 'activo';        
            $vehiculo->tarifa = $request->tarifa;
            $vehiculo->usuario_id = auth()->user()->id;    
            $vehiculo->save();
            DB::commit();

            $this->mensaje('exito', 'vehiculo registrada correctamente');
            return response()->json($this->mensaje, 200);
        } catch (\Exception $e) {
            DB::rollBack();           
            $this->mensaje('error', 'error' . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
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
        $vehiculo = Vehiculo::Find($id);
        if (!$vehiculo) {
            $this->mensaje('error', 'Vehiculo no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $vehiculo);

        return response()->json($this->mensaje, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VehiculoRequest $request, string $id)
    {
        
        DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $vehiculo =Vehiculo::Find($request->vehiculo_id);
            if (!$vehiculo) {
                throw new Exception('Vehiculo no encontrado');
            }
            $vehiculo->nombre = $request->nombre;
            $vehiculo->descripcion = $request->descripcion_vehiculo;                   
            $vehiculo->tarifa = $request->tarifa;            
            $vehiculo->save();          
            DB::commit();

            $this->mensaje("exito", "Vehiculo editado Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function cambiarEstadoVehiculos(String $id_vehiculo,request $request){
        

         DB::beginTransaction();
        try {

            // Encontrar el usuario por ID
            $vehiculo =Vehiculo::Find($id_vehiculo);

            if (!$vehiculo) {
                throw new Exception('Vehiculo no encontrado');
            }
            if ($request->estado == "activo") {
                $vehiculo->estado = "inactivo";
            }
            if ($request->estado == "inactivo") {
                $vehiculo->estado = "activo";
            }

            $vehiculo->save();          
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
        DB::beginTransaction();
        try {
            $vehiculo = Vehiculo::find($id);
            if (!$vehiculo) {
                throw new Exception('vehiculo no encontrado');
            }

            $vehiculo->delete();

            DB::commit();

            $this->mensaje("exito", "Vehiculo eliminado correctamente");

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
