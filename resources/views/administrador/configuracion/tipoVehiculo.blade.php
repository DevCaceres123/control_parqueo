@extends('principal')
@section('titulo', 'TIPO VEHICULO')
@section('contenido')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">TIPOS DE VEHICULOS</h4>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="abrirModalTipoVehiculo()">
                            <i class="fas fa-plus me-1"></i> Nuevo
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="tabla_tipoVehiculo">
                        <thead class="table-light">
                            <tr>
                                <th>Nº</th>
                                <th>NOMBRE</th>
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
<div class="modal fade" id="modal_tipoVehiculo" data-bs-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="tipoVehiculoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title m-0" id="tipoVehiculoModalLabel">Tipo Vehiculo</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    onclick="cerrarModelTipoVehiculo()"></button>
            </div>
            <div class="modal-body">
                <form id="form_tipoVehiculo" autocomplete="off" method="POST">
                    <input type="hidden" id="tipoVehiculo_id" name="tipoVehiculo_id">
                    <div class="mb-3 row">
                        <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control uppercase-input" id="nombre" name="nombre"
                                placeholder="Ingrese el nombre del Tipo de Vehiculo" required>
                            <div id="_nombre"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal"
                    onclick="cerrarModelTipoVehiculo()">Cerrar</button>
                <button type="button" id="btn_guardar_tipoVehiculo" class="btn btn-dark btn-sm">Guardar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- <script>
    let rutas = {
        listar          : "{{ route('tipoVehiculos.listar') }}",
        crear           : "{{ route('tipoVehiculos.store') }}",
        editar          : "{{ route('tipoVehiculos.edit', ':id') }}",
        actualizar      : "{{ route('tipoVehiculos.update', ':id') }}",
        eliminar        : "{{ route('tipoVehiculos.destroy', ':id') }}",
        cambiarEstado   : "{{ route('tipoVehiculos.show', ':id') }}"
    };
</script> --}}
<script src="{{ asset('js/configuracion/tipoVehiculo.js') }}"></script>
@endsection