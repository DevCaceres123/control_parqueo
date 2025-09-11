<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
        <a href="index.html" class="logo">
            <span>
                <img src="{{ asset('admin_template/images/logo-sm.png') }}" alt="logo-small" class="logo-sm">
            </span>
            <span class="">
                <img src="{{ asset('admin_template/images/logo-light.png') }}" alt="logo-large"
                    class="logo-lg logo-light">
                <img src="{{ asset('admin_template/images/logo-dark.png') }}" alt="logo-large"
                    class="logo-lg logo-dark">
            </span>
        </a>
    </div>
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label pt-0 mt-0">
                        <span>MENU</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inicio') }}" role="button" aria-expanded="false"
                            aria-controls="sidebarDashboards">
                            <i class="iconoir-home-simple menu-icon"></i>
                            <span>INICIO</span>
                        </a>
                    </li><!--end nav-item-->
                    <li class="nav-item">
                        <a class="nav-link" href="#usuarios" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="usuarios">
                            <i class="iconoir-fingerprint-lock-circle menu-icon"></i>
                            <span>ADMIN USUARIOS</span>
                        </a>
                        <div class="collapse " id="usuarios">
                            <ul class="nav flex-column">

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('user.index') }}">Usuarios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('permisos.index') }}">Permisos</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>Boletas</span>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="#boleta" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="boleta">
                            <i class="iconoir-compact-disc menu-icon"></i>
                            <span>CONTROL BOLETAS</span>
                        </a>
                        <div class="collapse " id="boleta">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('boletas.index') }}">Generar Boletas</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('listarBoletas.index') }}">Listar Boletas</a>
                                </li>

                            </ul>
                        </div>
                    </li>



                     <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>REPORTES</span>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="#reporte" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="reporte">
                            <i class="iconoir-compact-disc menu-icon"></i>
                            <span>REPORTES</span>
                        </a>
                        <div class="collapse " id="reporte">
                            <ul class="nav flex-column">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('reportes.index') }}">Reportes</a>
                                </li>
                              
                            </ul>
                        </div>
                    </li>


                    <li class="menu-label mt-2">
                        <small class="label-border">
                            <div class="border_left hidden-xs"></div>
                            <div class="border_right"></div>
                        </small>
                        <span>CONFIGURACIÓN</span>
                    </li>


                    <li class="nav-item">
                        <a class="nav-link" href="#configuracion" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="configuracion">
                            <i class="iconoir-compact-disc menu-icon"></i>
                            <span>CONFIGURACION</span>
                        </a>
                        <div class="collapse " id="configuracion">
                            <ul class="nav flex-column">

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('vehiculos.index') }}">Tipo Vehiculos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('atraso.index') }}">Conf. Atraso</a>
                                </li>

                            </ul>
                        </div>
                    </li>




                </ul><!--end navbar-nav--->
            </div>
        </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->
</div>
<div class="startbar-overlay d-print-none"></div>
