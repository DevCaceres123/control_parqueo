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
                                <i class="fas fa-receipt me-2"></i> BOLETAS OBSERVADAS
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card-body">                
                    <div class="table-responsive">
                        <table class="table" id="tabla_boletas_observadas">
                            <thead class="table-light">
                                <tr>
                                    <th>Nº</th>
                                    <th>VEHICULO</th>
                                    <th>PLACA</th>
                                    <th>CI</th>
                                    <th>ENTRADA</th>                                  
                                    <th>ESTADIA</th>                                  
                                    <th>ACCION</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/modulos/boletas/listarBoletasObservadas.js') }}" type="module"></script>
@endsection
