<?php

namespace App\Http\Controllers\Reporte;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class Controlador_reporte extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

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
}
