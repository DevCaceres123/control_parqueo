<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Login\UsuarioRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Boleta;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Controlador_login extends Controller
{
    /**
     * @version 1.0
     * @author  Rodrigo Lecoña Quispe <rodrigolecona97@gmail.com>
     * @param Controlador Administrar la parte de usuario resgistrados LOGIN
     * ¡Muchas gracias por preferirnos! Esperamos poder servirte nuevamente
     */


    /**
     * PARA EL INGRESO DEL USUARIO POR USUARIO Y CONTRASEÑA
     */
    private $mensajeError = "Usuario o contraseña inválidos";

    public function ingresar(Request $request)
    {
        if ($this->validarDatos($request)->fails()) {
            return $this->respuestaError('Todos los campos son requeridos');
        }

        $usuario = $this->buscarUsuario($request->usuario);

        if (!$usuario) {
            return $this->respuestaError($this->mensajeError);
        }

        if ($this->autenticarUsuario($request)) {
            session(['mostrar_alerta_boletas_vencidas' => true]);

            return $this->respuestaExitosa('Inicio de sesión con éxito');
        }

        return $this->respuestaError($this->mensajeError);
    }

    private function validarDatos(Request $request)
    {
        return Validator::make($request->all(), [
            'usuario' => 'required',
            'password' => 'required'
        ]);
    }

    private function buscarUsuario($usuario)
    {
        return User::where('usuario', $usuario)->first();
    }

    private function autenticarUsuario(Request $request)
    {
        $credenciales = [
            'usuario' => $request->usuario,
            'password' => $request->password,
            'estado' => 'activo',
        ];

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();
            return true;
        }

        return false;
    }

    private function respuestaExitosa($mensaje)
    {
        return response()->json(mensaje_mostrar('success', $mensaje));
    }

    private function respuestaError($mensaje)
    {
        return response()->json(mensaje_mostrar('error', $mensaje));
    }
    /**
     * FIN PARA EL INGRESO DEL USUARIO Y CONTRASEÑA
     */

    /**
     * PARA INGRESAR AL INICIO
     */
    public function inicio()
    {
        $data['menu']   = 0;
        //$data['usuario_estacion'] = User::with(['estacion'])->find(Auth::user()->id);
        $fecha_actual = Carbon::now()->format('Y-m-d');
        $data['total_vehiculos_ingresados'] = Boleta::whereDate('entrada_veh', $fecha_actual)->count();
        $data['vehiculos_por_tipo'] = DB::table('boletas as b')
                ->join('vehiculos as t', 't.id', '=', 'b.vehiculo_id')
                ->select('t.nombre as tipo', DB::raw('COUNT(b.id) as total'))
                ->whereDate('b.entrada_veh', $fecha_actual)
                ->whereNull('b.deleted_at')
                ->groupBy('t.nombre')
                ->get();

        $data['monto_generado'] = Boleta::whereDate('salida_veh', $fecha_actual)->where('estado_parqueo', 'salida')->sum('total');
        $data['boletas_emitidas'] = Boleta::whereDate('salida_veh', $fecha_actual)->where('estado_parqueo', 'salida')->count();
        $data['fecha_actual'] = Carbon::now()->translatedFormat('d \d\e F \d\e Y');

        $data['vehiculos_en_parqueo'] = DB::table('boletas as b')
                ->join('vehiculos as t', 't.id', '=', 'b.vehiculo_id')
                ->select('t.nombre as tipo', DB::raw('COUNT(b.id) as total'))
                ->where('b.estado_parqueo', 'ingreso')
                ->whereDate('b.entrada_veh', $fecha_actual)
                ->whereNull('b.deleted_at')
                ->groupBy('t.nombre')
                ->get();


        // return $data;

        return view('inicio', $data);
    }
    /**
     * FIN PARA INGRESAR AL INICIO
     */

    /**
     * CERRAR LA SESSIÓN
     */
    public function cerrar_session(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $data = mensaje_mostrar('success', 'Finalizó la session con éxito!');
        return response()->json($data);
    }
    /**
     * FIN DE CERRAR LA SESSIÓN
     */
}
