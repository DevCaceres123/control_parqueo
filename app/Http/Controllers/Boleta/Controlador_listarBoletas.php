<?php

namespace App\Http\Controllers\Boleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Contacto;
use App\Models\Vehiculo;
use App\Models\Color;
use App\Models\Config_atraso;
use App\Models\User;
use App\Models\Tarifas;
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
        if (!auth()->user()->can('control.listar_boleta.inicio')) {
            return redirect()->route('inicio');
        }

        $encargados_puesto = User::select('id', 'nombres', 'apellidos')
        ->where('estado', 'activo')
        ->get();

        $vehiculos = Vehiculo::select('id', 'nombre')->where('estado', 'activo')->get();
        $colores = Color::select('id', 'nombre', 'color')->get();
        $tarifas = Tarifas::select('id','nombre','precio')->where('estado', 'activo')->get();


        return view("administrador.boletas.listar", compact('encargados_puesto', 'vehiculos', 'colores','tarifas'));
    }




    public function listarTodasBoletas(Request $request)
    {

        $fecha_actual = $request->input('fecha') ? Carbon::parse($request->input('fecha'))->toDateString() : null;
        $encargado = $request->input('encargado') ?? null;


        $query = Boleta::with([
            'vehiculo' => function ($query) {
                $query->select(['id', 'nombre']);
            },
             'contacto' => function ($query) {  // nueva relación
                 $query->select(['id', 'telefono']); // campos que quieres traer
             },
        ])->select('id', 'dias_cobrados', 'placa', 'ci', 'entrada_veh', 'salida_veh', 'estado_parqueo', 'total', 'vehiculo_id', 'contacto_id')->orderBy('id', 'desc');



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
                'contacto' => $boleta->contacto ? $boleta->contacto->telefono : null,
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
                'editar' => auth()->user()->can('control.listar_boleta.editar'),
                'eliminar' => auth()->user()->can('control.listar_boleta.eliminar'),
                'entrada' => auth()->user()->can('control.listar_boleta.ticket_entrada'),
                'salida' => auth()->user()->can('control.listar_boleta.ticket_salida'),
                'contacto' => auth()->user()->can('control.listar_boleta.contacto'),
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
            $tarifa = $reporteJson['tarifa_vehiculo'] ?? null;
            $tipo_vehiculo = $reporteJson['tipo_vehiculo'] ?? null;
            $fechaEntrada = $boleta->entrada_veh ?? null;
            $numeroBoleta = $boleta->num_boleta ?? null;
            $datosUsuario = $reporteJson['usuario'] ?? null;
            $nombre = $reporteJson['nombre'] ?? null;
            $ci = $reporteJson['ci'] ?? null;
            $fecha_finalizacion =  $reporteJson['fecha_finalizacion'] ?? null;
            $color = $reporteJson['color'] ?? null;
            $contacto = $reporteJson['contacto'] ?? null;


            // Genera el reporte en PDF
            $reporteBase64 = $this->generarBoletaEntrada($placa, $tarifa, $fechaEntrada, $fecha_finalizacion, $numeroBoleta, $datosUsuario, $nombre, $ci, $color, $contacto, $tipo_vehiculo);

            // Responde con éxito
            $this->mensaje('exito', $reporteBase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }



    public function generarBoletaEntrada($placa, $tarifa, $fecha_actual, $fecha_finalizacion, $codigoUnico, $datosUsuario, $nombre, $ci, $color, $contacto, $tipo_vehiculo)
    {


        $data = [
            'usuario' => $datosUsuario,
            'tarifa_vehiculo' =>  json_decode(json_encode($tarifa)),
            'tipo_vehiculo' =>  json_decode(json_encode($tipo_vehiculo)),
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

    public function reporteDiario(Request $request)
    {


        try {

            $usuarioActual = auth()->user()->id;
            $reporte = DB::table('boletas as b')
                ->join('tarifas as t', 'b.tarifa_id', '=', 't.id')
                ->select(
                    't.precio as tarifa_bs',
                    // Cantidades
                    DB::raw("SUM(CASE WHEN b.dias_cobrados = 1 THEN 1 ELSE 0 END) as boletas_a_tiempo"),
                    DB::raw("SUM(CASE WHEN b.dias_cobrados <> 1 THEN 1 ELSE 0 END) as boletas_con_atraso"),
                    DB::raw("COUNT(b.id) as total_boletas"),
                    // Totales monetarios
                    DB::raw("SUM(CASE WHEN b.dias_cobrados = 1 THEN b.total ELSE 0 END) as ingresos_a_tiempo"),
                    DB::raw("SUM(CASE WHEN b.dias_cobrados <> 1 THEN b.total ELSE 0 END) as ingresos_por_atraso"),
                    DB::raw("SUM(CASE WHEN b.dias_cobrados <> 1 THEN b.monto_atraso ELSE 0 END) as monto_atraso"),
                    DB::raw("SUM(b.total) as ingresos_totales")
                )
                ->whereDate('b.salida_veh', $request->fecha)
                ->whereNull('b.deleted_at')
                ->where('b.usuario_id', $usuarioActual)
                ->groupBy('t.precio')
                ->orderBy('t.precio')
                ->get();


            $pdf = PDF::loadView('administrador/boletas/reporteUsuario', [
                'reporte' => $reporte,
                'fecha' => Carbon::parse($request->fecha)->format('d-m-Y'),
                'usuario_generador' => auth()->user()->only(['nombres', 'apellidos']),
            ]);

            $pdfContent = $pdf->output();

            $this->mensaje("exito", base64_encode($pdfContent));
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {


            $this->mensaje("error", "error" . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }
    public function VerificarRegistrosPasados()
    {
        
        try {
            // ✅ Solo ejecutamos si la sesión indica que acaba de iniciar sesión
            if (!session()->pull('mostrar_alerta_boletas_vencidas', false)) {
                // Si no hay bandera, devolvemos que no se muestre nada
                $this->mensaje("vacio", "No hay alerta pendiente");
                return response()->json($this->mensaje, 200);
            }

            $hoy = Carbon::now();

            // Boletas con más de 30 días
            $boletasVencidas = Boleta::whereDate('entrada_veh', '<', $hoy->copy()->subDays(30))->count();
            $listaBoletas = Boleta::select('placa', 'ci')
                ->whereDate('entrada_veh', '<', now()->subDays(30))
                ->get();

            $data = [
                'cantidad' => $boletasVencidas,
                'boletas'  => $listaBoletas,
            ];

            $this->mensaje("exito", $data);
            return response()->json($this->mensaje, 200);

        } catch (Exception $e) {
            $this->mensaje("error", "error: " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
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
        $boleta = Boleta::with([
            'contacto' => function ($query) {  // nueva relación
                $query->select(['id', 'telefono']); // campos que quieres traer
            },
        ])->select('id', 'placa', 'ci', 'vehiculo_id', 'contacto_id', 'color_id', 'estado_parqueo','tarifa_id')
          ->where('id', $id)
          ->first();

        if ($boleta->estado_parqueo == 'salida') {
            $this->mensaje('error', 'No se puede editar una boleta pagada');
            return response()->json($this->mensaje, 200);
        }

        if (!$boleta) {
            $this->mensaje('error', 'boleta no encontrada');
            return response()->json($this->mensaje, 200);
        }
        $this->mensaje("exito", $boleta);

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
            $boleta = Boleta::find($id);
            if (!$boleta) {
                throw new Exception('boleta no encontrado');
            }
            // actualizamos los datos de la boleta
            $boleta->placa = $request->placa;
            $boleta->ci = $request->ci;
            $boleta->vehiculo_id = $request->vehiculo_id;
            $boleta->color_id = $request->color_id;
            $boleta->tarifa_id = $request->tarifa_id;

            $datos_voleta = json_decode($boleta->reporte_json, true);

            $vehiculo = Vehiculo::find($request->vehiculo_id);
            $tarifa = Tarifas::find($request->tarifa_id);
            $color = Color::find($request->color_id);
            // cambiamos los datos del json
            $datos_voleta['tarifa_vehiculo']['nombre'] = $tarifa->nombre ?? null;
            $datos_voleta['tarifa_vehiculo']['precio'] = $tarifa->precio ?? null;
            $datos_voleta['tipo_vehiculo']['nombre'] = $vehiculo->nombre ?? null;
            $datos_voleta['color'] = $color->nombre ?? null;
            $datos_voleta['contacto'] = $request->contacto ?? null;
            $datos_voleta['placa'] = $request->placa ?? null;
            $datos_voleta['ci'] = $request->ci ?? null;
            $boleta->reporte_json = json_encode($datos_voleta);

            // actualizamos o creamos el contacto
            $contactoModel = Contacto::firstOrCreate(['telefono' => $request->contacto]);
            $boleta->contacto_id = $contactoModel->id;

            // guardarmos la informacion actualizada
            $boleta->save();
            DB::commit();

            $this->mensaje("exito", "Datos de boleta actualizados correctamente");

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
