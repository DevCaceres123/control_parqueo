@extends('principal')
@section('titulo', 'CONFIGURAR RETRASO VEHICULO')
@section('contenido')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3">
                    <div class="row align-items-center">
                        <div class="col">
                             <h4  class="card-title mb-0 text-light fw-bold text-uppercase">
                                <i class="fas fa-tools  me-2"></i>  configuracion de atraso
                            </h4>
                        </div>
                        <div class="col-auto">
                        
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="tabla_atraso">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>TIEMPO EXTRA</th>
                                    {{-- <th>TARIFA</th> --}}
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
    <div class="modal fade" id="modal_atraso" data-bs-backdrop="static" tabindex="-1" role="dialog"
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
                    <form id="form_atraso">
                        <input type="hidden" id="atraso_id" name="atraso_id">
                        <div class=" row p-1">
                            <div class="col-12 col-md-12">
                                <label for="nombre" class="col-form-label">Tiempo Extra <strong
                                        class="text-danger">(*)</strong></label>
                                <input type="time" class="form-control uppercase-input" id="tiempo_extra" name="tiempo_extra" required>
                                <div id="_tiempo_extra"></div>

                            </div>

                            {{-- <div class="col-12 col-md-12">
                                <label for="nombre" class="col-form-label">Tarifa <strong
                                        class="text-danger">(*)</strong></label>
                                <input type="number" class="form-control uppercase-input" id="monto" name="monto" required placeholder="Ingrese el monto a cobrar por el atraso">
                                <div id="_monto"></div>
                            </div> --}}

                            
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm limpiar_modal"
                        data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn_guardar_atraso" class="btn btn-dark btn-sm">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script src="{{ asset('js/modulos/configuracion/conf_atraso.js') }}" type="module"></script>

@endsection
