<div class="startbar d-print-none">
    <!--start brand-->
    <div class="brand">
        <div class="logo" style="display: block ; width: 100% !important; height: 70px; overflow: hidden;">
            <span style="width: 100%; height: 100%;">
                <img src="{{ asset('assets/logo-caranavi.webp') }}" alt="logo-small" class="" width="70px"
                    height="70px" style="object-fit: contain">
            </span>
            <span class="">

            </span>
        </div>
    </div>
    <!--end brand-->
    <!--start startbar-menu-->
    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <!-- Navigation -->
                <ul class="navbar-nav mb-auto w-100">
                    @can('inicio')
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
                    @endcan
                    <li class="nav-item">
                        @can('admin')
                            <a class="nav-link" href="#usuarios" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="usuarios">
                                <i class="iconoir-fingerprint-lock-circle menu-icon"></i>
                                <span>ADMIN USUARIOS</span>
                            </a>
                            <div class="collapse " id="usuarios">
                                <ul class="nav flex-column">
                                    @can('admin.usuario.inicio')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('user.index') }}">Usuarios</a>
                                        </li>
                                    @endcan

                                    @can('admin.rol.inicio')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('roles.index') }}">Roles</a>
                                        </li>
                                    @endcan
                                    @can('admin.permiso.inicio')
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('permisos.index') }}">Permisos</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        @endcan
                    </li>
                    @can('control')
                        <li class="menu-label mt-2">
                            <small class="label-border">
                                <div class="border_left hidden-xs"></div>
                                <div class="border_right"></div>
                            </small>
                            <span>Boletas</span>
                        </li>
                    @endcan


                    <li class="nav-item">
                        @can('control')
                            <a class="nav-link" href="#boleta" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="boleta">
                                <i class="fas fa-file-invoice menu-icon"></i>
                                <span>CONTROL BOLETAS</span>
                            </a>
                        @endcan
                        <div class="collapse " id="boleta">
                            <ul class="nav flex-column">
                                @can('control.boleta.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('boletas.index') }}">Generar Boletas</a>
                                    </li>
                                @endcan

                                @can('control.listar_boleta.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('listarBoletas.index') }}">Listar Boletas</a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>


                    @can('reportes')
                        <li class="menu-label mt-2">
                            <small class="label-border">
                                <div class="border_left hidden-xs"></div>
                                <div class="border_right"></div>
                            </small>
                            <span>REPORTES</span>
                        </li>
                    @endcan


                    <li class="nav-item">
                        @can('reportes')
                            <a class="nav-link" href="#reporte" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="reporte">
                                <i class="fas fa-chart-bar menu-icon"></i>
                                <span>REPORTES</span>
                            </a>
                        @endcan

                        <div class="collapse " id="reporte">
                            <ul class="nav flex-column">
                                @can('reportes.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('reportes.index') }}">Reportes</a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>

                    @can('config')
                        <li class="menu-label mt-2">
                            <small class="label-border">
                                <div class="border_left hidden-xs"></div>
                                <div class="border_right"></div>
                            </small>
                            <span>CONFIGURACIÓN</span>
                        </li>
                    @endcan


                    <li class="nav-item">
                        @can('config')
                            <a class="nav-link" href="#configuracion" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="configuracion">
                                <i class="fas fa-cog menu-icon"></i>
                                <span>CONFIGURACION</span>
                            </a>
                        @endcan
                        <div class="collapse " id="configuracion">
                            <ul class="nav flex-column">
                                @can('config.vehiculos.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('vehiculos.index') }}">Tipo Vehiculos</a>
                                    </li>
                                @endcan

                                @can('config.atraso.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('atraso.index') }}">Conf. Atraso</a>
                                    </li>
                                @endcan
                                @can('config.colores.inicio')
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('colores.index') }}">Colores</a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>




                </ul><!--end navbar-nav--->
            </div>
        </div><!--end startbar-collapse-->
    </div><!--end startbar-menu-->
</div>
<div class="startbar-overlay d-print-none"></div>
