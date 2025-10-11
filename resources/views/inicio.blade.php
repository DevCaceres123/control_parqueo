@extends('principal')
@section('titulo', 'INICIO')
@section('contenido')
    <div class="row g-4">
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
                                    <span>{{$vehiculo->tipo}}</span>
                                    <span class="fw-semibold text-dark">{{$vehiculo->total}}</span>
                                </li>
                            @endforeach


                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
