<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Boleta;
use App\Models\Vehiculo;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // clase para generar pdf
use Exception;
use Carbon\Carbon;

class Controlador_reporte extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can('reportes.inicio')) {
            return redirect()->route('inicio');
        }

        $role = Role::where('name', 'encargado_puesto')->where('guard_name', 'web')->first();

        // Si el rol no existe lo creamos
        if (!$role) {
            $role = Role::create([
                'name' => 'encargado_puesto',
                'guard_name' => 'web',
            ]);
        }
        $encargados_puesto = User::select('id', 'nombres', 'apellidos')->role('encargado_puesto')->where('estado', 'activo')->get();
        return view("administrador.reportes.reportes", compact('encargados_puesto'));
    }


    public function generar_reporte(Request $request)
    {

        try {

            $validatedData = $request->validate([
                'fecha_inicio' => 'required|date|before_or_equal:fecha_final',
                'fecha_final' => 'required|date|after_or_equal:fecha_inicio',
                'usuario'      => 'array',           // tiene que ser un array
                'usuario.*'    => 'integer|exists:users,id' // cada elemento debe existir
            ]);

            $fecha_inicio = $request->fecha_inicio . " 00:00:00";
            $fecha_final  = $request->fecha_final  . " 23:59:59";
            $reporte = DB::table('boletas as b')
                ->join('vehiculos as v', 'b.vehiculo_id', '=', 'v.id')
                ->select(
                    'v.tarifa as tarifa_bs',
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
                ->whereBetween('b.salida_veh', [$fecha_inicio, $fecha_final])
                ->whereNull('b.deleted_at')
                // Filtrar por usuario(s) seleccionado(s)
                ->when(!empty($request->usuario), function ($query) use ($request) {
                    $query->whereIn('b.usuario_id', $request->usuario);
                })
                ->groupBy('v.tarifa')
                ->orderBy('v.tarifa')
                ->get();

          $reporteBase64 = $this->generarReporte($reporte, $request->fecha_inicio, $request->fecha_final,$request);

            // Responde con éxito
            $this->mensaje('exito', $reporteBase64);
            return response()->json($this->mensaje, 200);
        } catch (Exception $e) {
            // Manejo de excepciones
            $this->mensaje("error", "Error: " . $e->getMessage());
            return response()->json($this->mensaje, 200);
        }
    }


    public function generarReporte($datos, $fecha_inicio, $fecha_final,$request)
    {

       $usuariosSeleccionados = User::whereIn('id', $request->usuario ?? [])->get(['nombres', 'apellidos']);
        
        $pdf = PDF::loadView('administrador/reportes/reporteIngresos', [
        'reporte' => $datos,
        'fecha_inicio' => Carbon::parse($fecha_inicio)->format('d-m-Y'),
        'fecha_final' => Carbon::parse($fecha_final)->format('d-m-Y'),
        'usuario_generador' => auth()->user()->only(['nombres', 'apellidos']),
        'usuarios_seleccionados' => $usuariosSeleccionados,
    ]);



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
