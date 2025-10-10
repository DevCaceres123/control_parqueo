<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Color;
use Exception;

class Controlador_color extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colores = Color::orderBy('id', 'desc')->get();
        return view("administrador.configuracion.colores", compact('colores'));
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
            $request->validate([
                'nombre' => 'required|string|min:3|max:100',
                'codigo_color' => 'required|string|max:10',
            ]);

            DB::beginTransaction();

            Color::create([
                'nombre' => $request->nombre,
                'color' => $request->codigo_color,
            ]);

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Color registrado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', 'Error al registrar el color: ' . $e->getMessage());
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
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|min:3|max:100',
                'codigo_color' => 'required|string|max:10',
            ]);

            DB::beginTransaction();

            $color = Color::findOrFail($id);
            $color->update([
                'nombre' => $request->nombre,
                'color' => $request->codigo_color,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Color actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            DB::beginTransaction();
            $color = Color::where('id', $id);

            if (!$color) {
                throw new Exception('color no encontrado');
            }
            $color->delete();

            DB::commit();

            return redirect()
                ->back()
                ->with('success', '✅ Color eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with('error', '❌ Error al eliminar el color: ' . $e->getMessage());
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
