<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resumen de Servicios Técnicos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 30px;
            color: #222;
        }

        .brand {
            text-align: center;
            margin-bottom: 10px;
        }

        .brand img {
            height: 80px;
        }

        .title-top {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #003366;
            margin-bottom: 6px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table thead {
            background-color: #003366;
            color: white;
        }

        table th,
        table td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        .summary-table {
            margin-top: 40px;
            font-size: 12px;
            width: 60%;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ccc;
        }

        .summary-table td {
            padding: 8px;
            border: 1px solid #ccc;
        }

        .summary-table .label {
            font-weight: bold;
            color: #003366;
        }

        .firma-container {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
        }

        .firma-container img {
            height: 60px;
        }

        .firma-label {
            margin-top: 4px;
            color: #003366;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="brand">
        <img src="{{ public_path('images/LOGO.png') }}" alt="Apple Boss">
    </div>

    <div class="title-top">APPLE BOSS</div>
    <div class="subtitle">Resumen de Servicios Técnicos</div>

    <table>
        <thead>
            <tr>
                <th>Código Nota</th>
                <th>Cliente</th>
                <th>Equipo</th>
                <th>Servicio</th>
                <th>Técnico</th>
                <th>Registrado por</th>
                <th>Costo</th>
                <th>Venta</th>
                <th>Fecha</th>
            </tr>
        </thead>

        <tbody>
        @php
            $totalCosto = 0;
            $totalVenta = 0;
        @endphp

        @foreach ($filas as $fila)
            @php
                $totalCosto += $fila['costo'];
                $totalVenta += $fila['venta'];
            @endphp

            <tr>
                <td>{{ $fila['codigo_nota'] }}</td>
                <td>{{ ucfirst($fila['cliente']) }}</td>
                <td>{{ $fila['equipo'] }}</td>
                <td>{{ ucfirst($fila['servicio']) }}</td>
                <td>{{ $fila['tecnico'] }}</td>
                <td>{{ $fila['vendedor'] }}</td>
                <td>{{ number_format($fila['costo'], 2) }} Bs</td>
                <td>{{ number_format($fila['venta'], 2) }} Bs</td>
                <td>{{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td class="label">📦 Total Costo Invertido:</td>
            <td>{{ number_format($totalCosto, 2) }} Bs</td>
        </tr>
        <tr>
            <td class="label">💰 Total Ingresos por Ventas:</td>
            <td>{{ number_format($totalVenta, 2) }} Bs</td>
        </tr>
        <tr>
            <td class="label">📈 Ganancia Neta:</td>
            <td>
                <strong style="color: {{ ($totalVenta - $totalCosto) >= 0 ? 'green' : 'red' }}">
                    {{ number_format($totalVenta - $totalCosto, 2) }} Bs
                </strong>
            </td>
        </tr>
    </table>

    <div class="firma-container">
        <img src="{{ public_path('images/firma.png') }}" alt="Firma">
        <div class="firma-label">Firma autorizada - Apple Boss</div>
    </div>

</body>
</html>
