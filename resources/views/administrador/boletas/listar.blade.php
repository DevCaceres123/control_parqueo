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

                        <div class="col-6 col-md-auto mb-3">
                            <label for="filterFecha" class="mb-2">Filtrar por fecha:</label>
                            <input type="date" id="filterFecha" class="form-control"
                                value="{{ \Carbon\Carbon::now()->toDateString() }}" />
                        </div>

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

                        <div class="col-auto col-md-3 mt-3">
                            <button id="btnListarTodo" class="btn btn-success mt-1">
                                <i class="fas fa-clipboard-list me-1"></i>Listar Todo</button>
                        </div>



                        {{-- <div class="col-auto mt-3  ms-1 position-absolute end-0 top-0">
                                    <a href="" id="reporte_diario"
                                        class="btn btn-primary mt-1" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Reporte Diario</a>
                                </div>                         --}}
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

    <!-- Modal para nuevo y editar -->
    <div class="modal fade" id="modal_tipoVehiculo" data-bs-backdrop="static" tabindex="-1" role="dialog"
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
                        <input type="hidden" id="vehiculo_id" name="vehiculo_id">
                        <div class=" row p-1">
                            <div class="col-12 col-md-12">
                                <label for="nombre" class="col-sm-2 col-form-label">Nombre <strong
                                        class="text-danger">(*)</strong></label>
                                <input type="text" class="form-control uppercase-input" id="nombre" name="nombre"
                                    placeholder="Ingrese el nombre del Tipo de Vehiculo" required>
                                <div id="_nombre"></div>


                            </div>

                            <div class="col-12 col-md-12">
                                <label for="nombre" class="col-form-label">Descripcion <strong
                                        class="text-danger">(*)</strong></label>
                                <textarea class="form-control" placeholder="Ingrese alguna descripcion del tipo de vehiculo" id="descripcion_vehiculo"
                                    style="height: 100px" name="descripcion_vehiculo"></textarea>
                                <div id="_descripcion_vehiculo"></div>
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
                    <button id="btn_guardar_tipoVehiculo" class="btn btn-dark btn-sm">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{{ asset('js/modulos/boletas/listar.js') }}" type="module"></script>

@endsection
