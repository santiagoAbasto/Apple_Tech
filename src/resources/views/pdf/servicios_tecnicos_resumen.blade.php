<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Servicios Técnicos</title>

    <style>
        @page { margin: 30px; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header img {
            height: 65px;
            margin-bottom: 6px;
        }

        .title {
            font-size: 17px;
            font-weight: bold;
            color: #003366;
        }

        .subtitle {
            font-size: 11px;
            color: #555;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        thead {
            background-color: #003366;
            color: #fff;
        }

        th {
            text-transform: uppercase;
            font-size: 8.5px;
        }

        .text-right {
            text-align: right;
        }

        .resumen {
            margin-top: 20px;
            width: 45%;
            float: right;
            font-size: 10px;
        }

        .resumen td {
            padding: 6px;
            border: 1px solid #ccc;
        }

        .label {
            font-weight: bold;
            color: #003366;
        }
    </style>
</head>

<body>

    <div class="header">
        <img src="{{ public_path('images/logo-appletech.jpeg') }}">
        <div class="title">APPLE TECHNOLOGY</div>
        <div class="subtitle">Resumen de Servicios Técnicos</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Servicio</th>
                <th>Técnico</th>
                <th>Vendedor</th>
                <th class="text-right">Costo (Bs)</th>
                <th class="text-right">Cobro (Bs)</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalCosto = 0.0;
                $totalVenta = 0.0;
            @endphp

            @forelse ($filas as $fila)
                @php
                    $costo = (float) ($fila['costo'] ?? 0);
                    $venta = (float) ($fila['venta'] ?? 0);

                    $totalCosto += $costo;
                    $totalVenta += $venta;
                @endphp
                <tr>
                    <td>{{ $fila['codigo_nota'] }}</td>
                    <td>{{ $fila['cliente'] }}</td>
                    <td>{{ $fila['equipo'] }}</td>
                    <td>{{ strtoupper($fila['servicio']) }}</td>
                    <td>{{ $fila['tecnico'] }}</td>
                    <td>{{ $fila['vendedor'] }}</td>
                    <td class="text-right">{{ number_format($costo, 2) }}</td>
                    <td class="text-right">{{ number_format($venta, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;">
                        No existen registros
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="resumen">
        <tr>
            <td class="label">Total Costo</td>
            <td class="text-right">{{ number_format($totalCosto, 2) }} Bs</td>
        </tr>
        <tr>
            <td class="label">Total Cobrado</td>
            <td class="text-right">{{ number_format($totalVenta, 2) }} Bs</td>
        </tr>
        <tr>
            <td class="label">Ganancia Neta</td>
            <td class="text-right">
                <strong style="color: {{ ($totalVenta - $totalCosto) >= 0 ? '#198754' : '#dc3545' }}">
                    {{ number_format($totalVenta - $totalCosto, 2) }} Bs
                </strong>
            </td>
        </tr>
    </table>

</body>
</html>
