<?php

namespace App\Http\Controllers\Boleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Vehiculo;
use App\Models\Config_atraso;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf; // clase para generar pdf
use Illuminate\Support\Facades\DB;

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
             'contacto' => function ($query) {  // nueva relación
                 $query->select(['id', 'telefono']); // campos que quieres traer
             },
        ])->select('id', 'dias_cobrados', 'placa', 'ci', 'entrada_veh', 'salida_veh', 'estado_parqueo', 'total', 'vehiculo_id','contacto_id')->orderBy('id', 'desc');



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
                'retraso' => $boleta->retraso,
                'placa' => $boleta->placa,
                'ci' => $boleta->ci,
                'estado_parqueo' => $boleta->estado_parqueo,
                'dias_cobrados' => $boleta->dias_cobrados,
                'total' => $boleta->total,
                'contacto'=>$boleta->contacto ? $boleta->contacto->telefono : null,
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


    public function generarTicketEntrada(string $id)
    {

        try {
            $boleta = Boleta::find($id);

            if (!$boleta) {
                throw new Exception("Reporte no encontrado");
            }

            // Decodifica el campo JSON como objeto
            $reporteJson = json_decode($boleta->reporte_json, true);


            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("El campo reporte_json no contiene un JSON válido.");
            }

            // Acceso a datos del JSON como array
            $placa = $reporteJson['placa'] ?? null;
            $vehiculo = $reporteJson['tarifa_vehiculo'] ?? null;
            $fechaEntrada = $boleta->entrada_veh ?? null;
            $numeroBoleta = $boleta->num_boleta ?? null;
            $datosUsuario = $reporteJson['usuario'] ?? null;
            $nombre = $reporteJson['nombre'] ?? null;
            $ci = $reporteJson['ci'] ?? null;
            $fecha_finalizacion =  $reporteJson['fecha_finalizacion'] ?? null;
            $color = $reporteJson['color'] ?? null;
            $contacto = $reporteJson['contacto'] ?? null;


            // Genera el reporte en PDF
            $reporteBase64 = $this->generarBoletaEntrada($placa, $vehiculo, $fechaEntrada, $fecha_finalizacion, $numeroBoleta, $datosUsuario, $nombre, $ci, $color, $contacto);

            // Responde con éxito
            $this->mensaje('exito', $reporteBase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function generarBoletaEntrada($placa, $vehiculo, $fecha_actual, $fecha_finalizacion, $codigoUnico, $datosUsuario, $nombre, $ci, $color, $contacto)
    {

        $data = [
            'usuario' => $datosUsuario,
            'tarifa_vehiculo' =>  json_decode(json_encode($vehiculo)),
            'fecha_generada' => $fecha_actual,
            'fecha_finalizacion' => $fecha_finalizacion,
            'placa' => $placa ?? null,
            'nombre' => $nombre ?? null,
            'ci' => $ci ?? null,
            'codigoUnico' => $codigoUnico,
            'color' => $color ?? null,
            'contacto' => $contacto ?? null,
        ];


        $pdf = Pdf::loadView('administrador/boletas/boletaPago', $data)
           ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir el contenido binario a Base64
        return  base64_encode($pdfContent);

    }



    public function generarTicketSalida(string $id)
    {

        try {
            $boleta = Boleta::find($id);

            if (!$boleta) {
                throw new Exception("Reporte no encontrado");
            }

            if (!$boleta->reporteSalida_json) {
                throw new Exception("boleta de salida no generada");
            }


            // Decodifica el campo JSON como objeto
            $reporteJson = json_decode($boleta->reporteSalida_json, true);



            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("El campo reporte_json no contiene un JSON válido.");
            }


            // Genera el reporte en PDF
            $reporteBase64 = $this->generarBoletaSalida($reporteJson);

            // Responde con éxito
            $this->mensaje('exito', $reporteBase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function generarBoletaSalida($datos)
    {

        // Pasar todo el array a la vista
        $pdf = Pdf::loadView('administrador/boletas/boletaPagada', $datos)
            ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir a Base64
        return base64_encode($pdfContent);
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
            $boleta = Boleta::find($id);
            if (!$boleta) {
                throw new Exception('Boleta no encontrado');
            }

            $boleta->delete();

            DB::commit();

            $this->mensaje("exito", "Boleta eliminada correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            DB::rollBack();

            $this->mensaje("error", "Error " . $e->getMessage());

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
