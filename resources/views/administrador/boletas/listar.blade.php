@extends('principal')
@section('titulo', 'LISTAR BOLETAS')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0 text-light fw-bold">
                                <i class="fas fa-receipt me-2"></i> LISTAR BOLETAS
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row position-relative">
                        @can('control.listar_boleta.fecha')
                            <div class="col-6 col-md-auto mb-3">
                                <label for="filterFecha" class="mb-2">Filtrar por fecha:</label>
                                <input type="date" id="filterFecha" class="form-control"
                                    value="{{ \Carbon\Carbon::now()->toDateString() }}" />
                            </div>
                        @endcan

                        @can('control.listar_boleta.usuario')
                            <div class="col-12 col-md-auto mb-3">
                                <label for="filterFecha" class="mb-2">Filtrar por encargados:</label>
                                <select class="form-select" aria-label="Default select example " name="encargados"
                                    id="encargados">
                                    <option selected disabled>Encargados</option>
                                    @foreach ($encargados_puesto as $item)
                                        <option value="{{ $item->id }}" class="text-capitalize">
                                            {{ $item->nombres }} {{ $item->apellidos }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endcan
                        @can('control.listar_boleta.listar_todo')
                            <div class="col-auto col-md-3 mt-3">
                                <button id="btnListarTodo" class="btn btn-success mt-1">
                                    <i class="fas fa-clipboard-list me-1"></i>Listar Todo</button>
                            </div>
                        @endcan

                        @can('control.listar_boleta.reporte_diario')
                            <div class="col-auto mt-3  ms-1 position-absolute end-0 top-0">
                                <button id="reporte_diario" class="btn btn-primary mt-1" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Reporte Diario
                                </button>
                            </div>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table" id="tabla_boletas">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>VEHICULO</th>
                                    <th>PLACA</th>
                                    <th>CI</th>
                                    <th>ENTRADA</th>
                                    <th>SALIDA</th>
                                    <th>ESTADIA</th>
                                    <th>ESTADO</th>
                                    <th>TOTAL</th>
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal para editar vehículo -->
    <div class="modal fade" id="editar_registro" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="editarVehiculoLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content shadow-lg rounded-3 border-0">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title fw-semibold">
                        <i class="fas fa-edit me-2"></i> Editar Vehículo
                    </h4>
                    <span class="ms-3 text-light small">
                        Campos obligatorios <strong class="text-danger">(*)</strong>
                    </span>
                    <button type="button" class="btn-close btn-close-white limpiar_modal" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <form id="form_editar_datos">
                    <div class="modal-body">
                        <small class="text-muted fst-italic mb-3">
                            Ingrese <strong>solo Placa</strong> o <strong>Documento de Identidad</strong>.
                        </small>
                        <input type="hidden" id="registro_id" name="registro_id">

                        <div class="row g-3">
                            <!-- PLACA -->
                            <div class="col-12 col-md-6">
                                <label for="placa" class="form-label">Placa <strong
                                        class="text-danger">*</strong></label>
                                <input type="text" class="form-control text-uppercase" id="placa" name="placa"
                                    placeholder="Ej: ABC-123">
                                <div id="_placa"></div>
                            </div>

                            <!-- CI -->
                            <div class="col-12 col-md-6">
                                <label for="ci" class="form-label">Documento de Identidad <strong
                                        class="text-danger">*</strong></label>
                                <input type="text" class="form-control" id="ci" name="ci"
                                    placeholder="Ej: 7896543">
                                <div id="_ci"></div>
                            </div>

                            <div class="col-md-12">
                                <label for="contacto" class="form-label mb-1 fw-semibold input-label">
                                    Contacto
                                </label>
                                <input type="number" id="contacto" name="contacto" class="form-control input-comun"
                                    placeholder="Ej. 77712345">
                            </div>

                            <!-- COLOR -->
                            <div class="col-12">
                                <label for="color" class="form-label">Color <strong
                                        class="text-danger">*</strong></label>
                                <select class="form-select" id="color" name="color" required>
                                    <option value="" disabled>Seleccione un color</option>
                                    @foreach ($colores as $color)
                                        <option value="{{ $color->id }}">
                                            {{ strtoupper($color->nombre) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="_color"></div>
                            </div>

                            <!-- TIPO DE VEHICULO -->
                            <div class="col-12">
                                <label for="tipo_vehiculo" class="form-label">Vehículo <strong
                                        class="text-danger">*</strong></label>
                                <select class="form-select" id="tipo_vehiculo" name="tipo_vehiculo" required>
                                    <option value="" disabled>Seleccione un color</option>
                                    @foreach ($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id }}">
                                            {{ strtoupper($vehiculo->nombre) }} <----> {{ strtoupper($vehiculo->tarifa) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="_tipo_vehiculo"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-danger btn-sm limpiar_modal" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cerrar
                        </button>
                        <button id="btn_guardar_datos" class="btn btn-dark btn-sm">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>




@endsection

@section('scripts')

    <script src="{{ asset('js/modulos/boletas/listar.js') }}" type="module"></script>



@endsection
