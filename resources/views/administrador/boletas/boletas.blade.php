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
                        <div id="tipos-vehiculo" class="row g-2 mb-4">
                            {{-- Auto --}}
                            @foreach ($vehiculos as $vehiculo)
                                <div class="col-12 col-md-4">
                                    <div class="card tipo-card bg-white text-dark text-center p-3 shadow-sm border border-2 rounded-3"
                                        data-id="{{ $vehiculo->id }}" data-vehiculo="{{ $vehiculo->nombre }}"
                                        data-precio="{{ $vehiculo->tarifa }}" style="cursor:pointer">
                                        <i class="fas fa-car-side fa-2x text-dark mb-2"></i>
                                        <h6 class="fw-semibold mb-1 text-uppercase">{{ $vehiculo->nombre }}</h6>
                                        <span class="badge bg-light text-dark fs-16">Bs. {{ $vehiculo->tarifa }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- 2. Datos a registrar --}}
                        <h6 class="fw-bold mb-2 text-secondary">2️⃣ ¿Qué registrar?</h6>
                        <div class="mb-3 ps-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_placa" value="placa"
                                    checked>
                                <label class="form-check-label fw-semibold" for="modo_placa">Solo placa</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="modo" id="modo_cliente"
                                    value="cliente">
                                <label class="form-check-label fw-semibold" for="modo_cliente">Datos del cliente</label>
                            </div>

                        </div>

                        {{-- 3. Campos dinámicos --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-8 d-none" id="grupo_cliente">
                                <label class="form-label mb-1">Datos del cliente</label>
                                <input type="text" id="ci_cliente" name="ci_cliente" class="form-control shadow-sm mb-2"
                                    placeholder='Ingrese el documento de Identidad del Cliente'>
                                <input type="text" id="nombre_cliente" class="form-control shadow-sm"
                                    placeholder="Ingrese el nombre completo del Cliente">
                            </div>
                            <div class="col-md-6 " id="grupo_placa">
                                <div class="form-floating">
                                    <input type="text" id="placa"
                                        class="form-control form-control-lg text-uppercase fw-bold text-center fs-1 border-0 border-bottom border-primary rounded-0"
                                        placeholder="Ej. 1234-ABC">
                                    <label for="placa">Número de Placa</label>
                                </div>
                            </div>
                        </div>

                        {{-- Botón generar --}}
                        <button type="button" id="btn-generar" class="btn btn-primary fw-bold shadow-sm px-4">
                            <i class="fas fa-file-alt"></i> Generar
                        </button>
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

                                    <input type="radio" class="btn-check" name="filtro" id="filtro_ci" value="ci"
                                        autocomplete="off">
                                    <label class="btn btn-outline-dark" for="filtro_ci">CI</label>

                                    <input type="radio" class="btn-check" name="filtro" id="filtro_codigo" value="codigo"
                                        autocomplete="off">
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
                            <div class="p-3 border border-dark rounded fs-15"
                                style="max-width: 400px; margin:auto; font-family: 'Segoe UI', Arial, sans-serif;">
                                <h5 class="text-center text-success border-bottom pb-2 mb-3 fw-bold">
                                    Resumen de Cobro
                                </h5>

                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-semibold">N° Boleta:</span>
                                    <span id="print-num_boleta"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1"
                                    id="print-vehiculo-container">
                                    <span class="fw-semibold">Vehiculo:</span>
                                    <span id="print-vehiculo"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-semibold">Placa:</span>
                                    <span id="print-placa"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 d-none border-bottom pb-1"
                                    id="print-nombre-container">
                                    <span class="fw-semibold">Nombre:</span>
                                    <span id="print-nombre"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 d-none border-bottom pb-1"
                                    id="print-ci-container">
                                    <span class="fw-semibold">CI:</span>
                                    <span id="print-ci"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-semibold">Entrada:</span>
                                    <span id="print-entrada"></span>
                                </div>

                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-semibold">Salida Máx:</span>
                                    <span id="print-salida"></span>
                                </div>
                                <!-- Monto inicial -->
                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="fw-semibold">Monto Inicial:</span>
                                    <span id="print-monto_inicial"></span>
                                </div>

                                <!-- Monto por retraso -->
                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1 text-danger">
                                    <span class="fw-semibold">Monto por Retraso:</span>
                                    <span id="print-monto_retraso"></span>
                                </div>
                                <div class="bg-light p-1 rounded text-center mt-3 border border-success">
                                    <p class="mb-1 fw-semibold text-success fs-4">Total: Bs. <span
                                            class="fw-bold text-success mb-0" id="print-total"></span>
                                    </p>
                                </div>

                                <div class="text-center mt-2 text-muted">
                                    <p class="mb-0 fw-semibold">Salida Vehiculo:</p>
                                    <h4 class="mb-0" id="print-salida-hora"></h4>
                                </div>
                            </div>

                            {{-- Botón imprimir --}}
                            <button class="btn btn-success shadow-sm mt-1 w-100" id="btn-imprimir_pago">
                                <i class="fas fa-print"></i> Imprimir Pago
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/boletas/boletas.js') }}" type="module"></script>
@endsection
