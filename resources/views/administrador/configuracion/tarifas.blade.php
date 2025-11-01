@extends('principal')
@section('titulo', 'TARIFAS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-money-bill-wave  me-2"></i> TARIFAS
                            </h4>
                        </div>
                        <div class="col-auto">
                            @can('config.vehiculos.crear')
                                <button type="button" class="btn btn-primary nuevo_vehiculo">
                                    <i class="fas fa-plus me-1"></i> Nuevo
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_tarifas">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>NOMBRE</th>
                                    <th>PRECIO (Bs)</th>
                                    <th>ESTADO</th>                                    
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_tarifa" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="tipoVehiculoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded" id='titulo_modal'>

                        </span>
                    </h4>
                    <span class="ms-3 text-light">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close limpiar_modal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="vehiculos">
                        <input type="hidden" id="tarifa_id" name="tarifa_id">
                        <div class=" row p-1">
                            <div class="col-12 col-md-12">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre <strong
                                        class="text-danger">(*)</strong></label>
                                <input type="text" class="form-control uppercase-input" id="nombre" name="nombre"
                                    placeholder="Ingrese el nombre del Tipo de Vehiculo" required>
                                <div id="_nombre"></div>

                            </div>                           


                            <div class="col-12 col-md-12">
                                <label for="nombre" class="col-sm-2 col-form-label">Tarifa <strong
                                        class="text-danger">(*)</strong></label>
                                <input type="number" class="form-control uppercase-input" id="tarifa" name="tarifa"
                                    placeholder="Ingrese tarifa del vehiculo" required>

                                <div id="_tarifa"></div>
                            </div>

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm limpiar_modal"
                        data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn_guardar_tarifa" class="btn btn-dark btn-sm">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{{ asset('js/modulos/configuracion/vehiculos.js') }}" type="module"></script>

@endsection
