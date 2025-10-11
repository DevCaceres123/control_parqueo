@extends('principal')
@section('titulo', 'COLORES')
@section('contenido')
    <div class="row">
        <div class="col-12 col-md-8  card shadow-lg border-0 rounded-4 overflow-hidden m-auto mb-4">
            <div class="card">
                <div class="card-header bg-dark border-start border-5 border-primary py-3 rounded-4">
                    <div class="row align-items-center">
                        <div class="col">
                             <h4  class="card-title mb-0 text-light fw-bold">
                                 <i class="fas fa-palette  me-2"></i> LISTA DE COLORES
                            </h4>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary nuevo_vehiculo" data-bs-toggle="modal"
                                data-bs-target="#modal_color">
                                <i class="fas fa-plus me-1"></i> Nuevo
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table" id="tabla_colores">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>NOMBRE</th>
                                    <th>COLOR</th>
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($colores as $i => $color)
                                    <tr class="text-uppercase">
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $color->nombre }}</td>
                                        <td>
                                            <div
                                                style="width: 25px; height: 25px; background-color: {{ $color->color }}; border-radius: 50%; ">
                                            </div>
                                        </td>
                                        <td>
                                            {{-- Botón Editar --}}
                                            <button type="button" class="btn btn-warning btn-sm btn_editar_color"
                                                data-id="{{ $color->id }}" data-nombre="{{ $color->nombre }}"
                                                data-color="{{ $color->color }}" data-bs-toggle="modal"
                                                data-bs-target="#modal_color" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            {{-- Botón Eliminar --}}
                                            <form action="{{ route('colores.destroy', $color->id) }}" method="POST"
                                                class="d-inline form_eliminar_color">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm btn-eliminar" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo color -->
    <div class="modal fade" id="modal_color" data-bs-backdrop="static" tabindex="-1" role="dialog"
        aria-labelledby="colorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h4 class="modal-title">
                        <span class="badge badge-outline-light rounded" id="titulo_modal_color">
                            Nuevo Color
                        </span>
                    </h4>
                    <span class="ms-3 text-light">Campos obligatorios <strong class="text-danger">(*)</strong></span>
                    <button type="button" class="btn-close limpiar_modal_color" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('colores.store') }}" method="POST" id="form_color">
                        @csrf
                        <input type="hidden" id="color_id" name="color_id">

                        <div class="row p-2">
                            <!-- Nombre del color -->
                            <div class="col-12 mb-3">
                                <label for="nombre_color" class="form-label">Nombre
                                    <strong class="text-danger">(*)</strong>
                                </label>
                                <input type="text" class="form-control uppercase-input" id="nombre_color" name="nombre"
                                    placeholder="Ej: Rojo metálico, Azul cielo..." required>
                                <div id="_nombre_color"></div>
                            </div>

                            <!-- Selector de color -->
                            <div class="col-12 mb-3">
                                <label for="codigo_color" class="form-label">Seleccionar Color
                                    <strong class="text-danger">(*)</strong>
                                </label>
                                <input type="color" class="form-control" id="codigo_color" name="codigo_color"
                                    value="#000000" title="Elija un color" required>
                                <div id="_codigo_color"></div>
                            </div>
                        </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm limpiar_modal_color"
                        data-bs-dismiss="modal">Cerrar</button>
                    <button id="btn_guardar_color" class="btn btn-dark btn-sm">Guardar</button>
                </div>
                </form>
            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#tabla_colores').DataTable({});
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Detectar clic en botón eliminar
            document.querySelectorAll('.btn-eliminar').forEach(boton => {
                boton.addEventListener('click', function() {
                    const form = this.closest('form');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // Enviar el formulario si confirma
                        }
                    });
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = new bootstrap.Modal(document.getElementById('modal_color'));
            const tituloModal = document.getElementById('titulo_modal_color');
            const form = document.getElementById('form_color');
            const btnGuardar = document.getElementById('btn_guardar_color');

            // === Evento para el botón EDITAR ===
            document.querySelectorAll('.btn_editar_color').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    const color = this.getAttribute('data-color');

                    // Cambiar título y botón
                    tituloModal.textContent = 'Editar Color';
                    btnGuardar.textContent = 'Actualizar';

                    // Cambiar acción del formulario
                    form.setAttribute('action', `colores/${id}`);
                    form.method = 'POST'; // Laravel solo acepta GET/POST en formularios

                    // Agregar o actualizar el campo oculto _method
                    let methodInput = form.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        form.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    // Cargar valores
                    document.getElementById('color_id').value = id;
                    document.getElementById('nombre_color').value = nombre;
                    document.getElementById('codigo_color').value = color;
                });
            });

            // === Limpiar modal al cerrar o al presionar “Nuevo” ===
            document.querySelectorAll('.limpiar_modal_color, .nuevo_vehiculo').forEach(btn => {
                btn.addEventListener('click', function() {
                    form.reset();
                    tituloModal.textContent = 'Nuevo Color';
                    btnGuardar.textContent = 'Guardar';
                    form.setAttribute('action', '{{ route('colores.store') }}');
                    form.method = 'POST';

                    // Eliminar el input _method si existe
                    const methodInput = form.querySelector('input[name="_method"]');
                    if (methodInput) methodInput.remove();
                });
            });
        });
    </script>

    <script src="{{ asset('js/modulos/configuracion/conf_atraso.js') }}" type="module"></script>

@endsection
