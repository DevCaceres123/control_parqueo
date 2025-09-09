<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETA DE PAGO</title>
    <style>
        :root {
            --temaño_letra: 10px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 10px;
            color: #333;
        }

        .container_boleta {
            padding: 9px;
            border: 2px dashed #8b5050;
            border-radius: 8px;
            position: relative;
        }

        .info_empresa {
            text-align: center;
            margin-bottom: 10px;
        }

        .info_empresa h2 {
            font-size: var(--temaño_letra);
            margin-bottom: 5px;
            font-weight: 100;
            letter-spacing: 2px;
        }

        .info_empresa .datos_us_pu {
            position: relative;
            width: 100%;
            height: 20px;
            text-transform: capitalize;
        }

        .info_empresa span {
            font-size: var(--temaño_letra);
            margin: 4px 0;
            font-weight: bold;

        }

        .info_empresa .usuario {
            position: absolute;
            left: 0;
            top: 0;
        }

        .info_empresa .puesto {
            position: absolute;
            right: 0;
            top: 0;
        } 

        .info_empresa .precio {
            position: absolute;
            right: 0;
            top: 0;
        }

        .vehiculo {
            width: 100%;
            text-align: center;
            font-size: var(--temaño_letra);
            margin-top: 5px;
            padding: 5px 0px;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            text-transform: uppercase;
        }

        .vehiculo .placa {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 3px;
            display: block;
        }

        .vehiculo .ci {
            font-size: 14px;
            font-weight: bold;
            display: block;
        }

        .cod_unico {
            color: #6c757d;
            font-size: 12px;
            text-align: center;
            margin-top: 3px;
            letter-spacing: 2px;
        }

        .fechas {
            width: 100%;
            text-align: center;
            font-size: 13px;
            letter-spacing: 1px;
            margin: 5px 0px 3px 0px;            
            border-bottom: 1px solid #333;
        }

        /* Bloques de montos */
        .monto-bloque {          
            margin-top: 3px ;
            font-size: 13px;
            font-family: cursive;

        }

        .total-bloque {
            background: #f8f9fa;
            border: 1px solid #198754;
            border-radius: 5px;
            padding: 5px;
            margin-top: 8px;
            text-align: center;
        }

        .total-bloque h4 {
            color: #0d6efd;
            margin: 0;
        }

        .ley {
            margin-top: 8px;
            font-size: 8px;
            text-align: justify;
        }

        .ley .titulo_creacion {
            text-align: center;
            font-weight: 900;
        }
        .fecha_generada {
            margin: 10px 0px 0px 0px 0px;
            padding: 0px;
            text-align: center;
            font-size: 15px;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <p class="fecha_generada">
        {{$fecha_hoy}}
    </p>


    <div class="container_boleta">
        <!-- Información de la empresa -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTONOMO MUNICIPAL DE CARANAVI</h2>
            <hr>
            <h2>DIRECCION DE RECAUDACIONES</h2>
            <hr>

            <div class="datos_us_pu">
                <span class="usuario">
                    U.s.: {{ $usuario['nombres'][0] ?? 'N' }}. {{ $usuario['apellidos'][0] ?? '' }}
                </span>
                <span class="precio"><b>Bs.- </b>{{ $tarifa_vehiculo['tarifa'] ?? '0' }}.00</span>
            </div>
        </div>

        <!-- Datos del vehículo -->
        <div class="vehiculo">
            @if ($placa)
                <span class="placa">P. {{ strtoupper($placa) }}</span>
            @endif
            @if ($ci)
                <span class="ci">D. {{ $ci }}</span>
            @endif
            @if ($tarifa_vehiculo['nombre'])
                <span>{{ $tarifa_vehiculo['nombre'] }} |</span>
            @endif
            @if ($nombre)
                <span>{{ $nombre }} |</span>
            @endif
        </div>

        <!-- Código único -->
        <div class="cod_unico">
            <p>{{ $codigoUnico }}</p>
        </div>

        <!-- Fechas de entrada y salida -->
        <div class="fechas">
            <p>Entrada:</b> {{ $entrada_vehiculo ?? 'N/A' }}</p>
            <p>Salida:</b> {{ $salida_vehiculo ?? 'N/A' }}</p>           

        </div>

        <!-- Montos -->
        <div class="monto-bloque">
            <span>Monto Inicial:</span>
            <span>Bs. {{ $monto_vehiculo_boleta ?? '0' }}</span>
        </div>
        <div class="monto-bloque">
            <span>Monto Extra (Retraso):</span>
            <span>Bs. {{ $monto_extra ?? '0' }}</span>
        </div>
        <div class="total-bloque">
            <p class="fw-semibold text-success">Total</p>
            <h4><b>Bs. {{ $total ?? '0' }}</b></h4>
        </div>

        <!-- Ley -->
        <div class="ley">
            <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
            <p>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                MUNICIPAL DE CARANAVI</p>
        </div>
    </div>
</body>

</html>
