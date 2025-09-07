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
                    <div class="col-md-9 border-end">
                        {{-- 1. Tipo de vehículo --}}
                        <h6 class="fw-bold mb-3 text-secondary">1️⃣ Selecciona el tipo de vehículo:</h6>
                        <div id="tipos-vehiculo" class="row g-3 mb-4">
                            {{-- Auto --}}
                            @foreach ($vehiculos as $vehiculo)
                                <div class="col-6 col-md-4">
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
                    <div class="col-md-3 d-flex flex-column align-items-center justify-content-start">
                        <h6 class="fw-bold text-secondary mb-2">Vista previa</h6>
                        <iframe id="iframe-boleta" class="border rounded shadow-sm mb-2" style="width: 100%; height: 260px;"
                            title="Vista previa de la boleta"></iframe>
                        <button class="btn btn-success shadow-sm" id="btn-imprimir">
                            <i class="fas fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/boletas/boletas.js') }}" type="module"></script>
@endsection
