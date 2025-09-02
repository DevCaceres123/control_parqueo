<?php

use App\Http\Controllers\Configuracion\Controlador_vehiculo;
use App\Http\Controllers\Usuario\Controlador_login;
use App\Http\Controllers\Usuario\Controlador_permisos;
use App\Http\Controllers\Usuario\Controlador_rol;
use App\Http\Controllers\Usuario\Controlador_user;
use App\Http\Controllers\Usuario\Controlador_usuario;
use App\Http\Middleware\Autenticados;
use App\Http\Middleware\No_autenticados;
use Illuminate\Support\Facades\Route;



Route::prefix('/')->middleware([No_autenticados::class])->group(function(){
    Route::get('/', function(){
        return view('login');
    })->name('login');

    Route::get('/login', function(){
        return view('login', ['fromHome' => true]);
    })->name('login_home');

    Route::controller(Controlador_login::class)->group(function(){
        Route::post('ingresar', 'ingresar')->name('log_ingresar');
    });
});


Route::prefix('/admin')->middleware([Autenticados::class])->group(function(){
    Route::controller(Controlador_login::class)->group(function(){
        Route::get('inicio', 'inicio')->name('inicio');
        Route::post('cerrar_session', 'cerrar_session')->name('salir');
    });

    Route::controller(Controlador_usuario::class)->group(function(){
        Route::get('perfil', 'perfil')->name('perfil');
        Route::post('pwd_guardar', 'password_guardar')->name('pwd_guardar');
    });

    //PARA LOS PERMISOS
    Route::resource('permisos', Controlador_permisos::class);
    Route::post('/permisos/listar', [Controlador_permisos::class, 'listar'])->name('permisos.listar');

    //PARA EL ROL
    Route::resource('roles', Controlador_rol::class);

    //para la administracion de usuarios
    Route::resource('user', Controlador_user::class);
    Route::post('/user/listar', [Controlador_user::class, 'listar'])->name('user.listar');


    //PARA LA ADMINISTRACION DE VEHICULO

        // CONTROLADOR PARA LAS SEDES
    Route::controller(Controlador_vehiculo::class)->group(function () {
        Route::resource('vehiculos', Controlador_vehiculo::class);       
    });

});
