<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $rol1       = new Role();
        $rol1->name = 'administrador';
        $rol1->save();


        $rol2       = new Role();
        $rol2->name = 'encargado_puesto';
        $rol2->save();


        $usuario = new User();
        $usuario->usuario = 'admin';
        $usuario->password = Hash::make('1234');
        $usuario->ci = '10028685';
        $usuario->nombres = 'Admin';
        $usuario->apellidos = 'admin admin';
        $usuario->estado = 'activo';
        $usuario->email = 'maicol@gmail.com';
        $usuario->save();

        $usuario->syncRoles(['administrador']);

        $usuario2 = new User();
        $usuario2->usuario = '123456789';
        $usuario2->password = Hash::make('1234');
        $usuario2->ci = '123456789';
        $usuario2->nombres = 'michael manuel';
        $usuario2->apellidos = 'caceres quina';
        $usuario2->estado = 'activo';
        $usuario2->email = 'michael@gmail.com';
        $usuario2->save();

        $usuario2->syncRoles(['encargado_puesto']);



        Permission::create(['name' => 'inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'inicio.monto_generado'])->syncRoles([$rol1]);
        Permission::create(['name' => 'inicio.vehiculos_ingresados'])->syncRoles([$rol1]);

        Permission::create(['name' => 'admin'])->syncRoles([$rol1]);

        // USAURIO
        Permission::create(['name' => 'admin.usuario.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.usuario.crear'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.editar'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.eliminar'])->assignRole($rol1);
        Permission::create(['name' => 'admin.usuario.desactivar'])->assignRole($rol1);    

        //ROL
        Permission::create(['name' => 'admin.rol.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.rol.visualizar'])->syncRoles([$rol1]);

        //PERMISOS
        Permission::create(['name' => 'admin.permiso.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'admin.permiso.eliminar'])->syncRoles([$rol1]);


        // CONTROL DE BOLETAS
        Permission::create(['name' => 'control'])->syncRoles([$rol1,$rol2]);
        
        Permission::create(['name' => 'control.boleta.inicio'])->syncRoles([$rol1,$rol2]);

        Permission::create(['name' => 'control.listar_boleta.inicio'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'control.listar_boleta.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'control.listar_boleta.editar'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'control.listar_boleta.ticket_entrada'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'control.listar_boleta.ticket_salida'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'control.listar_boleta.contacto'])->syncRoles([$rol1,$rol2]);


        Permission::create(['name' => 'control.listar_boleta.fecha'])->syncRoles([$rol1,$rol2]);
        Permission::create(['name' => 'control.listar_boleta.usuario'])->syncRoles([$rol1]);
        Permission::create(['name' => 'control.listar_boleta.listar_todo'])->syncRoles([$rol1]);

        //REPORTES
        Permission::create(['name' => 'reportes'])->syncRoles([$rol1]);
        Permission::create(['name' => 'reportes.inicio'])->syncRoles([$rol1]);


        // CONFIGURACION
        Permission::create(['name' => 'config'])->syncRoles([$rol1]);

        // vehiculos
        Permission::create(['name' => 'config.vehiculos.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.vehiculos.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.vehiculos.eliminar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.vehiculos.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.vehiculos.desactivar'])->syncRoles([$rol1]);

        //tarfas
        Permission::create(['name' => 'config.tarifas.inicio'])->syncRoles([$rol1]);

        // conf.atraso
        Permission::create(['name' => 'config.atraso.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.atraso.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.atraso.desactivar'])->syncRoles([$rol1]);

        //colores
        Permission::create(['name' => 'config.colores.inicio'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.colores.crear'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.colores.editar'])->syncRoles([$rol1]);
        Permission::create(['name' => 'config.colores.eliminar'])->syncRoles([$rol1]);



    }

}
