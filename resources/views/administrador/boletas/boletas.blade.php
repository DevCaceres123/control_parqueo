@extends('principal')
@section('titulo', 'GENERAR BOLETA')

@section('contenido')
    <div class="container py-4">
        <div class="card shadow rounded-3">
            <div class="card-header bg-dark text-white fw-semibold">
                <i class="fas fa-file-invoice"></i> Generar Boleta
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- COLUMNA IZQUIERDA: Configuración --}}
                    <div class="col-md-8 border-end">
                        {{-- 1. Tipo de vehículo --}}
                        <h6 class="fw-bold mb-3 text-secondary">1️⃣ Selecciona el tipo de vehículo:</h6>
                        <div id="precios" class="row g-2 mb-1">
                            {{-- Auto --}}
                            @foreach ($tarifas as $tarifa)
                                <div class="col-12 col-md-4">
                                    <div class="card tipo-precios bg-white text-dark text-center p-3 shadow-sm border border-2 rounded-3"
                                        data-id="{{ $tarifa->id }}" data-tipo="{{ $tarifa->nombre }}"
                                        data-precio="{{ $tarifa->precio }}" style="cursor:pointer">
                                        <i class="fas fa-car-side fa-2x text-dark mb-2"></i>
                                        <h6 class="fw-semibold mb-1 text-uppercase">{{ $tarifa->nombre }}</h6>
                                        <span class="badge bg-light text-dark fs-16">Bs. {{ $tarifa->precio }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="border rounded p-4 shadow-lg formulario-entrada">

                            <h5 class="fw-bolder mb-3 text-primary-emphasis text-uppercase text-center">
                                📝 Registro de Ingreso
                            </h5>


                            <div class="mb-2">
                                {{-- 2. Datos a registrar --}}
                                <h6 class="fw-bold mb-3 text-secondary-emphasis">2️⃣ ¿Qué registrar?</h6>
                                <div class="mb-1 selector-modo">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="modo" id="modo_placa"
                                            value="placa" checked>
                                        <label class="form-check-label fw-semibold" for="modo_placa">Solo Placa</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="modo" id="modo_cliente"
                                            value="cliente">
                                        <label class="form-check-label fw-semibold" for="modo_cliente">Datos del
                                            Cliente</label>
                                    </div>
                                </div>
                            </div>

                            {{-- 3. Campos dinámicos y la Placa como foco principal --}}
                            <div class="row g-4 mb-4 align-items-end">

                                {{-- Grupo cliente --}}
                                <div class="col-md-12 d-none" id="grupo_cliente">
                                    <input type="text" id="ci_cliente" name="ci_cliente"
                                        class="form-control mb-2 input-cliente" placeholder="Documento de Identidad (CI)">
                                    <input type="text" id="nombre_cliente" name="nombre_cliente"
                                        class="form-control input-cliente" placeholder="Nombre Completo">
                                </div>

                                {{-- Grupo placa --}}
                                <div class="col-md-12" id="grupo_placa">
                                    <div class="form-floating input-placa">
                                        <input type="text" id="placa" name="placa"
                                            class="form-control form-control-lg text-uppercase fw-bolder text-center fs-1 border-0 border-bottom border-primary rounded-0 placa-input-estilo"
                                            placeholder="EJ. 1234-ABC">
                                        <label for="placa">NÚMERO DE PLACA</label>
                                    </div>
                                </div>
                            </div>

                            {{-- 4. Campos comunes --}}
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="tipo_vehiculo" class="form-label mb-1 fw-semibold input-label">
                                        <i class="fas fa-car me-1 text-primary"></i> Tipo de vehículo
                                    </label>
                                    <select name="vehiculo_id" id="vehiculo_id" class=" text-capitalize" required>
                                        <option value=" " disabled selected>Seleccionar Vehiculo</option>
                                        @foreach ($vehiculos as $vehiculo)
                                            <option class="text-capitalize" value="{{ $vehiculo->id }}">{{ strtoupper($vehiculo->nombre) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>                               
                                <div class="col-md-6">
                                    <label for="color_vehiculo" class="form-label mb-1 fw-semibold input-label">
                                        <i class="fas fa-car me-1 text-primary"></i> Color del vehículo
                                    </label>
                                    <select name="color_id" id="color_id" class=" text-capitalize" required>
                                        <option value=" " disabled selected>Seleccionar color</option>
                                        @foreach ($colores as $color)
                                            <option class="text-capitalize" value="{{ $color->id }}">{{ strtoupper($color->nombre) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                 <div class="col-md-12">
                                    <label for="contacto" class="form-label mb-1 fw-semibold input-label">
                                        <i class="fas fa-phone-alt me-1 text-primary"></i> Contacto
                                    </label>
                                    <input type="number" id="contacto" name="contacto" class="form-control input-comun"
                                        placeholder="Ej. 77712345">
                                </div>
                                 
                                <div class="col-md-12 d-flex align-items-end">
                                    {{-- Botón generar --}}
                                    <button type="button" id="btn-generar"
                                        class="btn btn-primary fw-bold w-100 btn-lg shadow-sm boton-generar">
                                        <i class="fas fa-file-alt me-2"></i> GENERAR TICKET
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- COLUMNA DERECHA: Vista previa --}}
                    <div class="col-md-4 d-flex flex-column align-items-center justify-content-start">
                        <h6 class="fw-bold text-secondary mb-3">
                            <i class="fas fa-eye"></i> Vista previa de la boleta
                        </h6>

                        {{-- Buscador con toggle buttons --}}
                        <div class="card w-100 shadow-sm border rounded mb-3 p-3">
                            <div class="mb-2 fw-semibold text-secondary">Buscar boleta</div>

                            {{-- Botones toggle --}}
                            <div class="d-flex mb-2">
                                <div class="btn-group w-100" role="group" aria-label="Filtro boleta">
                                    <input type="radio" class="btn-check" name="filtro" id="filtro_placa" value="placa"
                                        autocomplete="off" checked>
                                    <label class="btn btn-outline-dark" for="filtro_placa">Placa</label>

                                    <input type="radio" class="btn-check" name="filtro" id="filtro_ci"
                                        value="ci" autocomplete="off">
                                    <label class="btn btn-outline-dark" for="filtro_ci">CI</label>

                                    <input type="radio" class="btn-check" name="filtro" id="filtro_codigo"
                                        value="codigo" autocomplete="off">
                                    <label class="btn btn-outline-dark" for="filtro_codigo">Código</label>
                                </div>
                            </div>

                            {{-- Input de búsqueda --}}
                            <div class="input-group">
                                <input type="text" id="filtro_valor" class="form-control shadow-sm"
                                    placeholder="Ingrese valor">
                                <button id="btn-buscar" class="btn btn-success shadow-sm">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </button>
                            </div>
                        </div>

                        {{-- Vista previa en iframe --}}
                        <div class="card w-100 shadow-sm border rounded mb-2" id="contenedor_boleta">
                            <iframe id="iframe-boleta" class="border-0 rounded" style="width: 100%; height: 300px;"
                                title="Vista previa de la boleta"></iframe>
                        </div>

                        {{-- Boleta oculta para impresión --}}

                        <div id="contendor_cobrar" class="d-none col-12">
                            <div class="p-2 border border-dark rounded fs-14"
                                style="max-width: 360px; margin:auto; font-family: 'Courier New', monospace;">

                                {{-- Encabezado --}}
                                <h5 class="text-center fw-bold mb-3 text-uppercase">
                                    Resumen de Cobro
                                </h5>

                                {{-- Datos principales --}}
                                <p class="mb-1"><b>N° Boleta:</b> <span id="print-num_boleta"></span></p>
                                <p class="mb-1"><b>Vehículo:</b> <span id="print-vehiculo"></span></p>
                                <p class="mb-1"><b>Placa:</b> <span id="print-placa"></span></p>

                                <p class="mb-1 d-none" id="print-nombre-container"><b>Nombre:</b> <span
                                        id="print-nombre"></span></p>
                                <p class="mb-1 d-none" id="print-ci-container"><b>CI:</b> <span id="print-ci"></span>
                                </p>

                                <hr class="my-2">

                                {{-- Tiempos --}}
                                <p class="mb-1"><b>Entrada:</b> <span id="print-entrada"></span></p>
                                <p class="mb-1"><b>Recoger antes de:</b> <span id="print-salida"></span></p>
                                <p class="mb-1 fw-bold">
                                    Dias cobrados: <span id="print-tiempo_estadia" class=" text-primary "></span>
                                </p>



                                <hr class="my-2">

                                {{-- Montos --}}
                                <p class="mb-1"><b>Monto Inicial:</b> <span id="print-monto_inicial"></span></p>
                                <p class="mb-1 text-danger"><b>Monto por Retraso:</b> <span
                                        id="print-monto_retraso"></span></p>

                                <div class="text-center my-2 border-top border-bottom py-2">
                                    <h4 class="mb-0 text-success">TOTAL: Bs. <span id="print-total"></span></h4>
                                </div>

                                {{-- Hora de salida --}}
                                <p class="text-center mb-0">
                                    <b>Salida:</b> <span id="print-salida-hora"></span>
                                </p>
                            </div>

                            {{-- Botón imprimir --}}
                            <button class="btn btn-dark shadow-sm mt-2 w-100" id="btn-imprimir_pago">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>


                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Inicialización de Selectr
        document.addEventListener('DOMContentLoaded', function() {
            const selectElement = document.getElementById('color_id');
            const selectElement2 = document.getElementById('vehiculo_id');


            let selectrInstanceSede = new Selectr(selectElement, {
                searchable: true,
                placeholder: 'Busca o selecciona una opción...'
            });

            let selectrInstanceVehiculo= new Selectr(selectElement2, {
                searchable: true,
                placeholder: 'Busca o selecciona una opción...'
            });



        });
    </script>
    <script src="{{ asset('js/modulos/boletas/boletas.js') }}" type="module"></script>
@endsection
