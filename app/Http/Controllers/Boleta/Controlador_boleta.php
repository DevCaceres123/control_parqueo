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
                'fecha_generada' => $fecha_actual,
                'fecha_finalizacion' => $fecha_actual->copy()->addDay()->setTime(15, 0, 0),// formatear para fecha final,
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
            $repuesta=[
                'codigoUnico'=>$codigoUnico,
                'boleta'=>$boleta,
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
                    ->setTime(15, 0, 0);  // 15:00:00 (3 pm)

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
                    ->setTime(15, 0, 0);  // 15:00:00 (3 pm)

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
                        ->setTime(15, 0, 0);


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
        $boleta->retraso = null;
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
        $boleta->retraso = null;
        $boleta->estado_parqueo = 'ingreso';
        $boleta->estado_impresion = 'generado';
        $boleta->vehiculo_id = $request->id_vehiculo;
        $boleta->usuario_id = auth()->user()->id;

        $boleta->save();


        return $boleta->id;

    }

       public function marcarBoletaImpresa($CodigoQr){
       
        
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
