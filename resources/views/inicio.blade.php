@extends('principal')
@section('titulo', 'INICIO')
@section('contenido')

    <div class="row g-4">

        @can('inicio.vehiculos_en_parqueo')
           <div class="col-12 col-md-6 col-lg-3">
    <div class="card card-compact-summary shadow-lg border-0 rounded-4 h-100">
        @php
            // Calcular el total primero
            $total_vehiculos = 0;
            foreach ($vehiculos_en_parqueo as $vehiculo) {
                $total_vehiculos += $vehiculo->total;
            }
        @endphp

        {{-- Cabecera Compacta --}}
        <div class="card-header bg-dark text-white border-0 rounded-top-4 py-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-parking fa-xl me-3"></i>
                <div>
                    <h6 class="mb-0 fw-bold text-uppercase">Vehículos Parqueados</h6>
                    <p class="mb-0 small opacity-75" style="font-size: 0.7rem;">Resumen por tipo</p>
                </div>
            </div>
        </div>

        <div class="card-body p-3">
            
            {{-- Métrica Principal: Total Destacado (Tamaño reducido a display-5) --}}
            <div class="text-center mb-3 pb-2 border-bottom border-dashed-custom">
                <p class="text-muted mb-0 fw-semibold text-uppercase small" style="font-size: 0.7rem;">TOTAL ACTUAL</p>
                <h2 class="display-5 fw-bolder text-dark mb-0">{{ $total_vehiculos }}</h2>
            </div>

            {{-- Listado Detallado (Más Compacto) --}}
            <h6 class="fw-bold text-dark mb-2 small"><i class="fas fa-list-ul me-2"></i> Detalle:</h6>
            <ul class="list-group list-group-flush list-parqueo-detail">
                @forelse ($vehiculos_en_parqueo as $vehiculo)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1" style="font-size: 0.85rem;">
                        <span class="text-capitalize text-muted">{{ $vehiculo->tipo }}</span>
                        <span class="badge bg-secondary-subtle text-secondary fw-bold">{{ $vehiculo->total }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-center text-muted small py-1">No hay vehículos registrados.</li>
                @endforelse
            </ul>

        </div>
    </div>
</div>
        @endcan


        @can('inicio.monto_generado')
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body">
                        <!-- Encabezado -->
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                            <div>
                                <p class="text-muted mb-1 fw-semibold text-uppercase small">Monto Generado Hoy</p>
                                <h3 class="fw-bold mb-0 text-dark">Bs. {{ $monto_generado }}</h3>
                            </div>
                            <div class="bg-dark bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center"
                                style="width:60px; height:60px;">
                                <i class="fas fa-money-bill-wave text-dark fs-3"></i>
                            </div>

                        </div>

                        <!-- Contenido -->
                        <div class="mt-3">
                            <p class="mb-1 text-secondary fw-semibold">
                                <i class="far fa-calendar-alt me-1 text-dark"></i> Fecha:
                                <span class="text-dark fw-bold">{{ $fecha_actual }}</span>
                            </p>

                            <ul class="list-unstyled mt-2 mb-0">
                                <li class="d-flex justify-content-between">
                                    <span><i class="fas fa-ticket-alt me-2 text-success"></i>Boletas emitidas</span>
                                    <span class="fw-semibold text-dark">{{ $boletas_emitidas }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

        @can('inicio.vehiculos_ingresados')
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                            <div>
                                <p class="text-muted mb-1 fw-semibold text-uppercase small">Vehículos Ingresados</p>
                                <h3 class="fw-bold mb-0 text-dark">{{ $total_vehiculos_ingresados }}</h3>
                            </div>
                            <div class="bg-dark bg-opacity-10 rounded-circle d-flex justify-content-center align-items-center"
                                style="width:60px; height:60px;">
                                <i class="fas fa-car text-dark fs-3"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <p class="mb-1 text-secondary fw-semibold">
                                <i class="far fa-calendar-alt me-1 text-dark"></i> Fecha:
                                <span class="text-dark fw-bold">{{ $fecha_actual }}</span>
                            </p>
                            <ul class="list-unstyled mt-2 mb-0">
                                @foreach ($vehiculos_por_tipo as $vehiculo)
                                    <li class="d-flex justify-content-between text-capitalize">
                                        <span>{{ $vehiculo->tipo }}</span>
                                        <span class="fw-semibold text-dark">{{ $vehiculo->total }}</span>
                                    </li>
                                @endforeach


                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endcan

    </div>
@endsection
