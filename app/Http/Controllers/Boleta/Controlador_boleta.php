<?php

namespace App\Http\Controllers\Boleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Boleta;
use App\Models\Vehiculo;
use App\Models\Config_atraso;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // clase para generar pdf
use Carbon\Carbon;
use Hashids\Hashids;// clase para generar clave unica
use Exception;

class Controlador_boleta extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $hashids;

    public function __construct()
    {
        $this->hashids = new Hashids(env('HASHIDS_SALT', 'clave-secreta'), 10); // Sal y longitud mínima
    }

    public function index()
    {
        $vehiculos = Vehiculo::select('id', 'nombre', 'tarifa')->where('estado', 'activo')->orderBy('id', 'desc')->get();
        return view("administrador.boletas.boletas", compact('vehiculos'));
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

        try {
            $validatedData = $request->validate([
                'id_vehiculo' => 'required|exists:vehiculos,id',
                'modo'        => 'required|in:placa,cliente',
            ]);
            DB::beginTransaction();
            $fecha_actual = Carbon::now();

            if ($request->modo === 'placa') {
                $id_boleta = $this->guardarBoletaPlaca($request, $fecha_actual);
                $codigoUnico = $this->encrypt($id_boleta);
                $boleta = $this->generarBoletaPlaca($request->placa, $request->id_vehiculo, $fecha_actual, $codigoUnico);
            }

            if ($request->modo === 'cliente') {
                $id_boleta = $this->guardarBoletaDatos($request, $fecha_actual);
                $codigoUnico = $this->encrypt($id_boleta);
                $boleta = $this->generarBoletaDatosPersonales($request->nombre, $request->ci, $request->id_vehiculo, $fecha_actual, $codigoUnico);
            }

            $fecha_finalizacion = $this->calcularSalida($fecha_actual);

            $data = [
                'usuario' => auth()->user()->only(['nombres', 'apellidos']),
                'tarifa_vehiculo' => Vehiculo::select('tarifa', 'nombre')->where('id', $request->id_vehiculo)->first(),
                'fecha_generada' => $fecha_actual->format('Y-m-d H:i:s'),
                'fecha_finalizacion' => $fecha_actual->copy()->addDay()->setTime(12, 0, 0)->format('Y-m-d H:i:s'),// formatear para fecha final,
                'placa' => $request->placa ?? null,
                'nombre' => $request->nombre ?? null,
                'ci' => $request->ci ?? null,
                'codigoUnico' => $codigoUnico,
            ];

            $boletaEdit = Boleta::find($id_boleta);
            $boletaEdit->num_boleta = $codigoUnico;
            $boletaEdit->salidaMax = $fecha_finalizacion;
            $boletaEdit->reporte_json = json_encode($data);
            $boletaEdit->save();

            // creamos un arrary para enviar los datos de las boletas
            $repuesta = [
                'codigoUnico' => $codigoUnico,
                'boleta' => $boleta,
            ];
            DB::commit();

            $this->mensaje("exito", $repuesta);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Revertir los cambios si hay algún error
            DB::rollBack();

            $this->mensaje("error", "Error" . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    public function generarBoletaPlaca($placa, $id_vehiculo, $fecha_actual, $codigoUnico)
    {

        $fecha_finalizacion = $fecha_actual
                    ->copy()              // para no modificar $fecha_actual
                    ->addDay()            // +1 día
                    ->setTime(12, 0, 0);  // 15:00:00 (3 pm)

        $data = [
            'usuario' => auth()->user()->only(['nombres', 'apellidos']),
            'tarifa_vehiculo' => Vehiculo::select('tarifa', 'nombre')->where('id', $id_vehiculo)->first(),
            'fecha_generada' => $fecha_actual,
            'fecha_finalizacion' => $fecha_finalizacion,
            'placa' => $placa ?? null,
            'nombre' => null,
            'ci' => null,
            'codigoUnico' => $codigoUnico,
        ];

        $pdf = Pdf::loadView('administrador/boletas/boletaPago', $data)
           ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir el contenido binario a Base64
        return  base64_encode($pdfContent);

    }

    public function generarBoletaDatosPersonales($nombreCompleto, $documentoIdentidad, $id_vehiculo, $fecha_actual, $codigoUnico)
    {
        $fecha_finalizacion = $fecha_actual
                    ->copy()              // para no modificar $fecha_actual
                    ->addDay()            // +1 día
                    ->setTime(12, 0, 0);  // 15:00:00 (3 pm)

        $data = [
            'usuario' => auth()->user()->only(['nombres', 'apellidos']),
            'tarifa_vehiculo' => Vehiculo::select('tarifa', 'nombre')->where('id', $id_vehiculo)->first(),
            'fecha_generada' => $fecha_actual,
            'fecha_finalizacion' => $fecha_finalizacion,
            'placa' => $placa ?? null,
            'nombre' => $nombreCompleto ?? null,
            'ci' => $documentoIdentidad ?? null,
            'codigoUnico' => $codigoUnico,
        ];

        $pdf = Pdf::loadView('administrador/boletas/boletaPago', $data)
           ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir el contenido binario a Base64
        return  base64_encode($pdfContent);

    }

    //  Esta funcion nos servira para determiar la hora de salida del vehiculo
    public function calcularSalida($fecha_actual)
    {
        $config = Config_atraso::select('tiempo_extra')
                    ->where('estado', 'activo')
                    ->first();

        // Fecha base: siguiente día a las 15:00
        $fecha_finalizacion = $fecha_actual
                        ->copy()
                        ->addDay()
                        ->setTime(12, 0, 0);


        if ($config && $config->tiempo_extra) {
            // Convertimos el TIME a Carbon (usa hoy como fecha)
            $tiempoExtra = Carbon::createFromFormat('H:i:s', $config->tiempo_extra);

            // Sumamos horas, minutos y segundos al final
            $fecha_finalizacion->addHours($tiempoExtra->hour)
                      ->addMinutes($tiempoExtra->minute)
                      ->addSeconds($tiempoExtra->second); // opcional si hay segundos


        }

        return $fecha_finalizacion;
    }


    public function guardarBoletaPlaca(Request $request, $fecha_actual)
    {
        $validatedData = $request->validate([
            'placa' => 'required|min:3|max:20',

        ]);

        $boleta = new Boleta();
        $boleta->placa = $request->placa;
        $boleta->entrada_veh = $fecha_actual;        
        $boleta->estado_parqueo = 'ingreso';
        $boleta->estado_impresion = 'generado';
        $boleta->vehiculo_id = $request->id_vehiculo;
        $boleta->usuario_id = auth()->user()->id;

        $boleta->save();


        return $boleta->id;

    }


    public function guardarBoletaDatos(Request $request, $fecha_actual)
    {
        $validatedData = $request->validate([
            'nombre' => 'nullable|min:3|max:50',
            'ci' => 'required|min:3|max:30',

        ]);

        $boleta = new Boleta();
        $boleta->ci = $request->ci;
        $boleta->persona = $request->nombre;
        $boleta->entrada_veh = $fecha_actual;        
        $boleta->estado_parqueo = 'ingreso';
        $boleta->estado_impresion = 'generado';
        $boleta->vehiculo_id = $request->id_vehiculo;
        $boleta->usuario_id = auth()->user()->id;

        $boleta->save();


        return $boleta->id;

    }

    public function marcarBoletaImpresa($CodigoQr)
    {
        try {


            DB::beginTransaction();

            $boleta = Boleta::where('num_boleta', $CodigoQr)->first();
            if (!$boleta) {

                throw new Exception("Error la Boleta no existe");
            }

            // Actualizar el estado de impresión
            $boleta->estado_impresion = "impreso";  // Cambia según tu lógica de estados
            $boleta->save(); // Guardar cambios en la base de datos
            DB::commit();
            $this->mensaje('exito', "Impreso Correctamente");

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }

    }


    public function buscarBoleta(Request $request)
    {


        try {

            $validatedData = $request->validate([
                'valor' => 'required',
                'filtro'        => 'required|in:codigo,ci,placa',
            ]);


            $boleta = Boleta::select('id', 'num_boleta', 'placa', 'ci', 'persona', 'entrada_veh', 'salidaMax', 'vehiculo_id', 'estado_parqueo')
                        ->where($validatedData['filtro'] === 'codigo' ? 'num_boleta' : $validatedData['filtro'], $validatedData['valor'])
                        ->first();
            
            
            if (!$boleta) {
                throw new Exception("no se encontro boletas con ese dato");
            }


            if ($boleta->estado_parqueo === 'salida') {
                throw new Exception("el vehículo con boleta {$boleta->num_boleta} ya ha salido del parqueo.");
            }

            $fecha_actual = Carbon::now();
            $montoRetraso = $this->calcularTotal($fecha_actual, $boleta->salidaMax, $boleta->entrada_veh);
            $vehiculo_monto = Vehiculo::select('nombre', 'tarifa')->where('id', $boleta->vehiculo_id)->first();
            $total = $vehiculo_monto->tarifa * ($montoRetraso['veces_pasadas'] + 1); // sumamos 1 al total para cobrar el primer dia
            $tiempoEstadita = $montoRetraso['tiempoPasado'];            
            $montoRetraso = $montoRetraso['veces_pasadas']  * $vehiculo_monto->tarifa;

            $data = [
                'datos_boleta' => $boleta,
                'total' => $total,
                'salida_vehiculo' => Carbon::now()->format('Y-m-d H:i:s'),//capturamos la hora actual con la hora de salida
                'datos_vehiculo' => $vehiculo_monto,
                'montoRetraso' => $montoRetraso,
                'montoVehiculo' => $vehiculo_monto->tarifa,
                'tiempoEstadia' => $tiempoEstadita,
                
            ];


            $this->mensaje("exito", $data);

            return response()->json($this->mensaje, 200);


        } catch (Exception $e) {


            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }
    }


    // Funcion que nos ayudara a calcular el total a cobrar
    public function calcularTotal($fecha_actual, $salida, $entrada)
    {
        // Precio fijo del atraso por cada bloque de 24h
        //$precioAtraso = Config_atraso::where('estado', 'activo')->first()->monto ?? 0;
        //$fechaActual = Carbon::parse('2025-09-10 15:30:00');
        $fechaActual = Carbon::parse($fecha_actual);
        $fechaEntrada = Carbon::parse($entrada);
        $fechaSalida = Carbon::parse($salida);
        // Días cobrados
        $diasCobrados = 1; // siempre al menos 1 dí

        // Si la fecha actual es menor a la fecha de entrada
        if ($fechaActual->lessThan($fechaEntrada)) {
            throw new Exception("la fecha actual no puede ser menor a la fecha de entrada");
        }

        // Si está dentro del tiempo límite <=
        if ($fechaActual->lte($fechaSalida)) {

            return [
                'tiempoPasado' => $diasCobrados,                
                'veces_pasadas' => 0,
            ];
        }

        
        // Calcular atraso
        $horasPasadas = $fechaSalida->diffInHours($fechaActual);        
        $vecesPasadas= ceil($horasPasadas / 24);
        $diasCobrados=$diasCobrados+$vecesPasadas;

        return [
                'tiempoPasado' => $diasCobrados,                
                'veces_pasadas' => $vecesPasadas,
            ];
    }

    public function formtearDiasHorasMinutos($minutosTotales)
    {
        
        $dias = floor($minutosTotales / 1440); // 1 día = 1440 minutos
        $horas = floor(($minutosTotales % 1440) / 60);
        $minutos = $minutosTotales % 60;

        return sprintf('%dd %02dh %02dm', $dias, $horas, $minutos);
    }

    // Se genera una boleta de pago
    public function boletaPagada(Request $request)
    {
        
        try {

            DB::beginTransaction();

            // Buscar la boleta por número
            $boleta = Boleta::where('num_boleta', $request->numeroBoleta)->first();

            if (!$boleta) {
                throw new Exception("La boleta con número {$request->numeroBoleta} no existe.");
            }

            // Verificar si está en el parqueo
            if ($boleta->estado_parqueo !== 'ingreso') {
                throw new Exception("El vehículo con boleta {$request->numeroBoleta} ya ha salido del parqueo.");
            }


            $boleta->salida_veh = $request->horaSalida;
            $boleta->estado_parqueo = 'salida';
            $boleta->total = $request->total;
            $boleta->dias_cobrados = $request->estadia;

            $reporte = $this->generarBoletaPago(
                $boleta->reporte_json,
                $boleta->entrada_veh,
                $request->horaSalida,
                $request->total,
                $boleta->salidaMax,
                $boleta->vehiculo_id,
                $request->retraso,
                $request->estadia,
            );

            $boleta->reporteSalida_json = $reporte['datos'];
            $boleta->monto_atraso = $reporte['monto_extra'];

            $boleta->save(); // Guardar cambios en la base de datos
            DB::commit();
            $this->mensaje('exito', $reporte['pdf']);

            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {

            DB::rollBack();
            $this->mensaje("error", "Error " . $e->getMessage());

            return response()->json($this->mensaje, 200);
        }


    }


    public function generarBoletaPago($datos, $entradaVehi, $salidaVeh, $total, $saliMaxVechiulo, $vehiculo_id, $retraso, $tiempoEstadia)
    {

        $fecha_hoy = Carbon::now();
        // Decodificar a array asociativo
        $datos = json_decode($datos, true);

        // Agregar los otros valores al array
        $datos['entrada_vehiculo'] = $entradaVehi;
        $datos['salida_vehiculo']  = $salidaVeh;
        $datos['total']            = $total;


        $vehiculo_monto = Vehiculo::select('tarifa')->where('id', $vehiculo_id)->first();
        $totalRetraso = $this->calcularTotal($salidaVeh, $saliMaxVechiulo, $entradaVehi);


        $totalBoleta = $vehiculo_monto->tarifa * ($totalRetraso['veces_pasadas'] + 1);

        // se valida que el total del frontend sea al mismo que se calcula aqui
        if ($totalBoleta != $total) {
            throw new Exception("los montos no coinciden");
        }

        $datos['monto_extra'] = $vehiculo_monto->tarifa * $totalRetraso['veces_pasadas'];
        $datos['monto_vehiculo_boleta'] = $vehiculo_monto->tarifa;        
        $datos['tiempo_estadia'] = $tiempoEstadia;


        // Pasar todo el array a la vista
        $pdf = Pdf::loadView('administrador/boletas/boletaPagada', $datos)
            ->setPaper([0, 0, 226.77, 841.89]); // 80 mm tamaño de papel

        // Obtener el contenido binario del PDF
        $pdfContent = $pdf->output();

        // Convertir a Base64
        return [
            'pdf' => base64_encode($pdfContent),
            'datos' =>  json_encode($datos),
            'monto_extra' => $datos['monto_extra']
            ];
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

    // Encriptar el ID
    public function encrypt($id)
    {
        return $this->hashids->encode($id);
    }

    // Desencriptar el ID
    public function decrypt($hashedId)
    {
        $decoded = $this->hashids->decode($hashedId);
        return count($decoded) > 0 ? $decoded[0] : null;
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
