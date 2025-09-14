<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETA DE PAGO</title>
    <style>
        :root {
            --font-size-base: 11px;
            --font-size-large: 14px;
            --font-size-small: 9px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 8px;
            color: #333;
        }

        .tiket {
            text-align: center;
            font-size: var(--font-size-large);
            margin: 10px 0 0 0;
            padding: 0;
            letter-spacing: 1px;
        }


        /* Contenedor */
        .container_boleta {
            padding: 10px;
            border: 2px dashed #8b5050;
            border-radius: 8px;
        }

        /* Encabezado */
        .info_empresa {
            text-align: center;
            margin-bottom: 6px;
        }

        .info_empresa h2 {
            font-size: var(--font-size-base);
            font-weight: normal;
            letter-spacing: 1px;
            border-bottom: 1px solid #333;
            margin-bottom: 3px;
        }

        /* Secciones */
        .section {
            border-top: 1px dashed #aaa;
            margin: 6px 0;
            padding: 4px 0;
            font-size: var(--font-size-base);
        }

        .usuario {
            float: left;
            font-weight: bold;
        }

        .precio {
            float: right;
            font-weight: bold;
        }


        .section::after {
            content: "";
            display: block;
            clear: both;
        }

        /* Vehículo */
        .vehiculo {
            text-align: center;
            font-size: var(--font-size-base);
            margin: 6px 0;
            padding: 4px 0;
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
            font-size: 13px;
            font-weight: bold;
            display: block;
        }

        /* Código único */
        .cod_unico {
            font-size: 14px;
            text-align: center;
            font-weight: bold;
            margin: 6px 0;
            padding: 4px 0;
            border: 1px dashed #555;
            border-radius: 5px;
            background: #f9f9f9;
            letter-spacing: 2px;
        }

        /* Fechas */
        .tabla_fechas {
            width: 100%;
            font-size: var(--font-size-base);
            border-bottom: 1px dashed #aaa;
        }

        .tabla_fechas td:first-child {
            text-align: left;
            font-weight: bold;
        }

        .tabla_fechas td:last-child {
            text-align: right;
        }

        /* Montos */
        .monto-bloque {
            margin-top: 4px;
            font-size: var(--font-size-base);
        }

        .total-bloque {
            background: #f8f9fa;
            font-weight: bold;
            border-radius: 5px;
            padding: 5px;
            margin-top: 8px;
            text-align: center;
            font-size: var(--font-size-large);
        }

        .total-bloque h4 {
            margin: 0;
        }

        /* Términos */
        .ley {
            margin-top: 8px;
            font-size: var(--font-size-small);
            text-align: justify;
            line-height: 1.2;
        }

        .ley .titulo_creacion {
            text-align: center;
            font-weight: bold;
            margin-bottom: 3px;
        }

        /* Mensaje final */
        .mensaje_final {
            text-align: center;
            font-size: var(--font-size-small);
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="tiket">
        <p>TICKET DE SALIDA</p>
    </div>

    <div class="container_boleta">
        <!-- Encabezado -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTONOMO MUNICIPAL DE CARANAVI</h2>
            <h2>DIRECCION DE RECAUDACIONES</h2>
        </div>

        <!-- Usuario + Precio -->
        <div class="section">
            <span class="usuario">
                U.s.: {{ $usuario['nombres'][0] ?? 'N' }}. {{ $usuario['apellidos'][0] ?? '' }}
            </span>
            <span class="precio"><b>Bs.- </b>{{ $tarifa_vehiculo['tarifa'] ?? '0' }}.00</span>
        </div>
        <!-- Datos del vehículo -->
        <div class="vehiculo">
            @if ($placa)
                <div>
                    <small style="font-size: 8px; font-weight: normal; display: block; color: #6c757d; font-weight: 400;">Placa</small>
                    <span class="placa">{{ strtoupper($placa) }}</span>
                </div>
            @endif

            @if ($ci)
                <div>
                    <small style="font-size: 8px; font-weight: normal; display: block;  color: #6c757d;  font-weight: 400;">Documento de Identidad</small>
                    <span class="ci">{{ $ci }}</span>
                </div>
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
            {{ $codigoUnico }}
        </div>

        <!-- Fechas -->
        <div class="section">
            <table class="tabla_fechas">
                <tr>
                    <td>H.Entrada</td>
                    <td>{{ $entrada_vehiculo ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>H.Salida</td>
                    <td>{{ $salida_vehiculo ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        <!-- Estadía y Retraso -->
        <div class="section">
            <table style="width:100%; text-align:center; font-size: var(--font-size-base);">
                <tr>
                    <td style="font-weight: bold;">Estadía</td>
                    <td style="font-weight: bold;">Retraso</td>
                </tr>
                <tr>
                    <td style="font-size: 14px; font-weight: 600;">
                        {{ $tiempo_estadia ?? '00:00' }}
                    </td>
                    <td style="font-size: 14px; font-weight: 600;">
                        {{ $tiempo_retraso ?? '00:00' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
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
                <h4>Total: <b>{{ $total ?? '0' }} Bs. </b></h4>
            </div>
        </div>

        <!-- Mensaje final -->
        <div class="mensaje_final">
            ¡Gracias por su preferencia!
        </div>
    </div>
</body>

</html>
