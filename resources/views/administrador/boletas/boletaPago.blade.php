<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETA DE PAGO</title>
    <style>
        :root {
            --font-size-base: 10px;
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
            font-size: var(--font-size-base);            
            padding: 0;
            letter-spacing: 1px;            
        }

        .lugar {
            position: absolute;
            top: 0;
            right: 0;
            font-weight: bold;
            margin-right: 35px;
        }

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
            margin-bottom: 2px;
        }

        /* Secciones */
        .section {
            border-top: 1px dashed #aaa;
            margin: 6px 0;
            padding: 4px 0;
            font-size: var(--font-size-base);
        }

        .info_Boleta {
            font-size: var(--font-size-base);
            position: relative;
            width: 100%;
            height: 14px;
            margin-bottom: 2px;
        }

        /* Usuario + Precio */
        .usuario {
            position: absolute;
            left: 0;

        }

        .precio {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);

        }

        .codigo {
            position: absolute;
            right: 0;

        }

        .section::after {
            content: "";
            display: block;
            clear: both;
        }

        /* Datos del vehículo */
        .vehiculo {
            text-align: center;
            font-size: var(--font-size-base);
            border-bottom: 1px solid #333;
            text-transform: uppercase;
        }

        .vehiculo .placa,
        .vehiculo .ci {

            display: block;
            font-weight: bold;
            letter-spacing: 2px;
        }

        /* Código único */
        .cod_unico {
            font-size: 13px;
            text-align: center;
            font-weight: bold;
            margin: 6px 0;
            padding: 4px 0;
            border: 1px dashed #555;
            border-radius: 5px;
            background: #f9f9f9;
            letter-spacing: 2px;
        }

        /* Tabla de fechas */
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

        /* Términos */
        .ley {
            margin-top: 6px;
            font-size: var(--font-size-small);
            text-align: justify;
            line-height: 1.2;
        }

        .ley .titulo_creacion {
            font-weight: bold;
            margin-bottom: 3px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="tiket">
        <p>TICKET DE INGRESO</p>
    </div>
    <div class="container_boleta">
        <!-- Encabezado -->
        <div class="info_empresa">
            <h2>GOBIERNO AUTONOMO MUNICIPAL DE CARANAVI</h2>
            <h2>DIRECCION DE RECAUDACIONES</h2>
            <h2>PARQUEO MUNICIPAL</h2>
        </div>

        <!-- Usuario + Precio -->
        <div class="info_Boleta">
            <span class="usuario">
                U.s.: {{ $usuario['nombres'][0] ?? 'N' }}. {{ $usuario['apellidos'][0] ?? '' }}.
            </span>
            <span class="precio">
                <b>Bs.- </b>{{ $tarifa_vehiculo->tarifa ?? 'N/A' }}.00
            </span>
            <span class="codigo">
                #{{ $codigoUnico }}
            </span>
        </div>

        <div class="vehiculo">
            @if ($placa)
                <div>
                    <small style=" font-weight: normal; display: block; color: #6c757d; font-weight: 400;">Placa</small>
                    <span class="placa cod_unico">{{ strtoupper($placa) }}</span>
                </div>
            @endif

            @if ($ci)
                <div>
                    <small style="font-weight: normal; display: block;  color: #6c757d;  font-weight: 400;">Documento
                        de Identidad</small>
                    <span class="ci cod_unico">{{ $ci }}</span>
                </div>
            @endif

            @if ($tarifa_vehiculo->nombre)
                <span style="">{{ $tarifa_vehiculo->nombre }} |</span>
            @endif
            @if ($color)
                <span style="">{{ $color ?? 'N/A' }} |</span>
            @endif
            @if ($nombre)
                <span style="">{{ $nombre }}|</span>
            @endif
            @if ($contacto)
                <span style="">Contacto: {{ $contacto ?? 'N/A' }}</span>
            @endif
        </div>
        <!-- Fechas -->
        <div class="section">
            <table class="tabla_fechas">
                <tr>
                    <td>H. Entrada</td>
                    <td>{{ $fecha_generada ? \Carbon\Carbon::parse($fecha_generada)->format('d-m-Y H:i:s') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>H. Recoger antes de:</td>
                    <td>{{ $fecha_finalizacion ? \Carbon\Carbon::parse($fecha_finalizacion)->format('d-m-Y H:i:s') : 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Términos y condiciones -->
        <div class="ley">
            <p class="titulo_creacion">TÉRMINOS Y CONDICIONES</p>
            <p>
                El vehículo será entregado únicamente al portador de esta boleta.
                Se recomienda cerrar bien puertas,ventanas y conservar la llave.
                La administración no se responsabiliza por robos o daños en el interior del vehículo.
            </p>
        </div>
    </div>
</body>

</html>
