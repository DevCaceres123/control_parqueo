<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOLETA DE PAGO</title>
    <style>
        /* Estilos optimizados */

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
            position: relative;
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


        .cod_precio {
            width: 100%;
            position: relative;
            width: 100%;
            height: 80px;
            font-size: 13px;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            font-weight: 900px;
        }

        .cod_precio .codigo {
            position: absolute;
            left: 0;
            top: 0;
        }

        .info_empresa .precio {
            position: absolute;
            right: 0;
            top: 0;
        }

        .qr_code {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);

            width: 80px;
            margin: auto;
            height: 80px;
            box-sizing: border-box;
        }

        .qr_code img {
            display: inline-block;
            margin: auto;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .vehiculo {
            width: 100%;
            text-align: center;
            font-size: var(--temaño_letra);
            margin-top: 3px;
            padding: 5px 0px;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            text-transform: uppercase;
        }

        .vehiculo .placa {
            font-size: 16px;
            display: block;
            margin-bottom: 2px;
            font-weight: bold;
            letter-spacing: 3px;
        }

          .vehiculo .ci {
            font-size: 16px;
            display: block;
            margin-bottom: 2px;
            font-weight: bold;
            letter-spacing: 3px;
        }


        .fechas {
            width: 100%;
            text-align: center;
            font-size: 13px;
            letter-spacing: 1px;
            margin-top: 5px;
        }

        .fecha_generada {
            margin: 10px 0px 0px 0px 0px;
            padding: 0px;
            text-align: center;
            font-size: 15px;
            letter-spacing: 1px;
        }

        .cod_unico{

            color: #6c757d;
            font-size: 12px;
            text-align: center;
            margin-top: 3px;
            letter-spacing: 2px;
        }

        .ley {
            margin-top: 5px;
            font-size: 8px;
            text-align: justify;
        }

        ley p {
            margin-top: 3px;
        }

        .ley .titulo_creacion {
            text-align: center;
            font-weight: 900;

        }
    </style>
</head>

<body>
    <p class="fecha_generada">

        {{ $fecha_generada ?? 'N/A' }}
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
                    U.s. : {{ $usuario['nombres'][0] ?? 'N' }}. {{ $usuario['apellidos'][0] ?? ' ' }}.

                    {{ isset($usuario['apellidos']) && strpos($usuario['apellidos'], ' ') !== false ? $usuario['apellidos'][strpos($usuario['apellidos'], ' ') + 1] : ' ' }}
                </span>

                <span class="precio"><b>Bs.- </b>{{ $tarifa_vehiculo->tarifa ?? 'N/A' }}.00</span>
            </div>


        </div>

        <div class="vehiculo">
            @if ($placa != null)
                <span class="placa">P. {{ strtoupper($placa) }}</span>
            @endif

            @if ($ci != null)
                <span class="ci">D. {{ $ci }}</span>
            @endif

            @if ($tarifa_vehiculo->nombre != null)
                <span class=''> {{ $tarifa_vehiculo->nombre }} |</span>
            @endif    

            @if ($nombre != null)
                <span> {{ $nombre }} |</span>
            @endif

        </div>


        <div class="cod_unico">
            <p class="codigo_unico">{{$codigoUnico}}</p>
        </div>

        <div class="fechas">
            <p>
                Expira el:
                {{ $fecha_finalizacion ?? 'N/A' }}
            </p>
        </div>



        <div class="ley">
            <p class="titulo_creacion">LEY AUTÓNOMA MUNICIPAL N.º 61/2024</p>
            <P>LEY MUNICIPAL DE CREACION DE LA TASA DE RODAJE-PEAJE DEL GOBIERNO AUTÓNOMO
                MUNICIPAL DE CARANAVI</P>
        </div>


    </div>
</body>

</html>
