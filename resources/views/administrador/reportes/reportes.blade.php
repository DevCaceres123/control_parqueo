@extends('principal')
@section('titulo', 'REPORTES')
@section('contenido')

    <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-primary text-white text-center rounded-top-4 p-3">
                <h4 class="card-title mb-0 fw-bold fs-5 text-uppercase">
                    <i class="fas fa-file-archive  me-2"></i> Reporte de Boletas
                </h4>
            </div>
            <div class="card-body p-4">
                <form id="form-reporte_usuario">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio_usuario" class="form-label fw-semibold">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fecha_inicio_usuario"
                                name="fecha_inicio_usuario" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            <div class="invalid-feedback">
                                Por favor, seleccione una fecha de inicio.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_final_usuario" class="form-label fw-semibold">Fecha de Final</label>
                            <input type="date" class="form-control" id="fecha_final_usuario"
                                name="fecha_final_usuario" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            <div class="invalid-feedback">
                                Por favor, seleccione una fecha de finalización.
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-semibold">Seleccionar Encargado(s) de Puesto</label>
                            <div class="border border-secondary rounded p-3 bg-light">
                                <div class="form-check form-check-inline mb-2">
                                    <input class="form-check-input" type="checkbox" id="select_all_user">
                                    <label class="form-check-label fw-bold" for="select_all_user">
                                        Seleccionar Todos
                                    </label>
                                </div>
                                <hr class="my-2">
                                <div class="row" id="mesesPagados">
                                    @foreach ($encargados_puesto as $encargados)
                                        <div class="col-12 col-md-6">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" id="user_{{ $encargados->id }}"
                                                    name="encargados_puesto[]" value="{{ $encargados->id }}">
                                                <label class="form-check-label text-uppercase" for="user_{{ $encargados->id }}">
                                                    {{ $encargados->nombres }} {{ $encargados->apellidos }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-12 col-md-6">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input listar-turnos" type="checkbox"
                                                name="listar_turnos" value="1" id="listar_turnos">
                                            <label class="form-check-label text-uppercase text-danger" for="listar_turnos">
                                                Listar Turnos
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" id="btn-reporte_usuario">
                                <i class="fas fa-file-alt me-2"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@section('scripts')

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            let fecha_inicio_usuario = document.getElementById("fecha_inicio_usuario");
            let fecha_final_usuario = document.getElementById("fecha_final_usuario");
         
            // Al cambiar la fecha de inicio, actualizamos el mínimo de la fecha final
            fecha_inicio_usuario.addEventListener("change", function() {
                fecha_final_usuario.min = fecha_inicio_usuario.value;
            });

            // Al cambiar la fecha final, actualizamos el máximo de la fecha de inicio
            fecha_final_usuario.addEventListener("change", function() {
                fecha_inicio_usuario.max = fecha_final_usuario.value;
            });

        });


        document.getElementById("listar_turnos").addEventListener("change", function() {
            document.getElementById("listar_turnos_hidden").disabled = this.checked;
        });
    </script>

    <script src="{{ asset('js/modulos/reporte/reporte.js') }}" type="module"></script>
@endsection
