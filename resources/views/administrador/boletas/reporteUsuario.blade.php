<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte diario</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
            background-color: #fff;
            /* Asegura un fondo blanco para el PDF */
            -webkit-print-color-adjust: exact;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        /* Nuevo: Contenedor principal con borde y sombra */
        .report-wrapper {

            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            background-color: #fff;
        }

        .header-container {
            position: relative;
            height: 100px;
            /* Altura suficiente para el logo */

        }

        .header-info {
            text-align: center;
            line-height: 1.2;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 70%;
        }

        h2 {
            font-size: 16px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0;
        }

        h4 {
            font-size: 12px;
            color: #1a1a1a;
            margin: 0;
            font-weight: 300;
        }

        h5 {
            font-size: 11px;
            font-weight: normal;
            color: #555;
            margin: 0;
        }

        .header-logo {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
        }

        img.logo {
            width: 80px;
            height: 85px;
        }

        .report-details {
            font-size: 10px;
            color: #777;
            text-align: left;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .report-details p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            border-left: 1px solid #ddd;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #f5f5f5;
            color: #555;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .atraso td {
            background-color: #fff2f2;
            color: #880000;
        }

        .totales th,
        .totales td {
            background-color: #4CAF50;
            color: white;
            font-size: 12px;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .totales th {
            text-align: right;
        }

        .totales td {
            text-align: right;
        }

        .monetary-value {
            text-align: right;
            font-weight: bold;
        }

        .firmaencargado {
            text-align: center;
            margin: 170px 0px 0px 0px;

        }

        .nombreFirma {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 13px;
        }

        .rol {
            margin-top: 0px;
            font-size: 12px;
        }

        .aclaracionFirma {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="report-wrapper">
        <div class="header-container">
            <div class="header-info">
                <h2>GOBIERNO AUTÓNOMO MUNICIPAL DE CARANAVI</h2>
                <h4>SECRETARIA MUNICIPAL ADMINISTRATIVA FINANCIERA</h4>
                <h5>PARQUEO MUNICIPAL</h5>
                <h5>Dirección de Recaudaciones</h5>

            </div>
            <div class="header-logo">
                <img src="{{ public_path('assets/logo-caranavi.webp') }}" alt="Logo" class="logo">
            </div>
        </div>

        <div class="report-details" style="margin-bottom: 20px;">
            <p><b>Reporte Generado Por:</b> {{ $usuario_generador['nombres'] ?? 'N/A' }}
                {{ $usuario_generador['apellidos'] ?? '' }}</p>
            <p><b>Periodo de Cobro:</b> {{ $fecha ?? 'N/A' }}</p>
            <p><b>Fecha de Emisión:</b> {{ now()->format('d-m-Y H:i:s') }}</p>
        </div>


        <table>
            <thead>
                <tr>
                    <th>Tarifa (Bs)</th>
                    <th>Boletas a tiempo</th>
                    <th>Boletas con atraso</th>
                    <th>Total Boletas</th>
                    <th class="monetary-value">Ingresos a tiempo (Bs)</th>
                    <th class="monetary-value">Ingresos por atraso (Bs)</th>
                    {{-- <th class="monetary-value">Monto atraso (Bs)</th> --}}
                    <th class="monetary-value">Ingresos totales (Bs)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totales = [
                        'cantidad_sin' => 0,
                        'cantidad_con' => 0,
                        'total_boletas' => 0,
                        'total_sin' => 0,
                        'total_con' => 0,
                        'monto_atraso' => 0,
                        'total_general' => 0,
                    ];
                @endphp

                @foreach ($reporte as $r)
                    @php
                        $totales['cantidad_sin'] += $r->boletas_a_tiempo;
                        $totales['cantidad_con'] += $r->boletas_con_atraso;
                        $totales['total_boletas'] += $r->total_boletas;
                        $totales['total_sin'] += $r->ingresos_a_tiempo;
                        $totales['total_con'] += $r->ingresos_por_atraso;
                        $totales['monto_atraso'] += $r->monto_atraso;
                        $totales['total_general'] += $r->ingresos_totales;
                    @endphp
                    <tr class="{{ $r->boletas_con_atraso > 0 ? 'atraso' : '' }}">
                        <td>{{ number_format($r->tarifa_bs, 2) }}</td>
                        <td>{{ $r->boletas_a_tiempo }}</td>
                        <td>{{ $r->boletas_con_atraso }}</td>
                        <td class="monetary-value">{{ $r->total_boletas }}</td>
                        <td class="monetary-value">{{ number_format($r->ingresos_a_tiempo, 2) }}</td>
                        <td class="monetary-value">{{ number_format($r->ingresos_por_atraso, 2) }}</td>
                        {{-- <td class="monetary-value">{{ number_format($r->monto_atraso, 2) }}</td> --}}
                        <td class="monetary-value">{{ number_format($r->ingresos_totales, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="totales">
                    <th colspan="3" style="text-align: right;">TOTALES</th>
                    <td class="monetary-value">{{ $totales['total_boletas'] }}</td>
                    <td class="monetary-value">{{ number_format($totales['total_sin'], 2) }}</td>
                    <td class="monetary-value">{{ number_format($totales['total_con'], 2) }}</td>
                    {{-- <td class="monetary-value">{{ number_format($totales['monto_atraso'], 2) }}</td> --}}
                    <td class="monetary-value" style="background-color: #880000">
                        {{ number_format($totales['total_general'], 2) }}</td>
                </tr>
            </tbody>

        </table>

        <div class="firmaencargado">
            <p>............................................................................</p>
            <p class="nombreFirma">
                {{ $usuario_generador['nombres'] ?? 'N/A' }}
                {{ $usuario_generador['apellidos'] ?? 'N/A' }}
            </p>
            <p class="rol">
                Operario(a) de Turno
            </p>
            
        </div>
    </div>
</body>

</html>
