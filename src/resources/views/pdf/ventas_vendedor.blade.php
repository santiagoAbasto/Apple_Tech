<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    @page { margin: 30px 28px; }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 10.5px;
      color: #1e1e1e;
      background-color: #fff;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #003366;
      padding-bottom: 8px;
    }

    .brand img {
      width: 130px;
    }

    .datos-vendedor {
      text-align: right;
      font-size: 10px;
      line-height: 1.4;
    }

    .titulo {
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      color: #003366;
      margin-top: 10px;
      text-transform: uppercase;
    }

    .fecha-rango {
      text-align: center;
      font-size: 10px;
      color: #555;
      margin-bottom: 8px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      font-size: 10px;
    }

    th, td {
      border: 1px solid #ccc;
      padding: 6px;
      text-align: left;
    }

    th {
      background-color: #003366;
      color: #fff;
    }

    .negativo {
      color: #dc3545;
      font-weight: bold;
    }

    .positivo {
      color: #003366;
      font-weight: bold;
    }

    .resumen {
      width: 100%;
      margin-top: 14px;
      font-size: 10.5px;
    }

    .resumen td {
      padding: 3px 5px;
    }

    .resumen tr td:first-child {
      text-align: right;
      font-weight: bold;
      width: 85%;
    }

    .resumen tr td:last-child {
      text-align: right;
      width: 15%;
      color: #003366;
    }

    footer {
      margin-top: 20px;
      font-size: 9.5px;
      border-top: 1px solid #ccc;
      padding-top: 8px;
      color: #444;
      display: flex;
      justify-content: space-between;
    }

    .footer-left p {
      margin: 2px 0;
    }

    .footer-right {
      text-align: right;
      font-size: 8px;
      line-height: 1.3;
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <div class="header">
    <div class="brand">
      <img src="{{ public_path('images/logo-appletech.jpeg') }}" alt="Apple Technology">
    </div>

    <div class="datos-vendedor">
      <p><strong>Vendedor:</strong> {{ $vendedor->name }}</p>
      <p><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</p>
    </div>
  </div>

  <div class="titulo">Resumen de Ventas del Vendedor</div>

  <div class="fecha-rango">
    Desde {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
    hasta {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
  </div>

  <!-- TABLA -->
  <table>
    <thead>
      <tr>
        <th>Cliente</th>
        <th>Producto</th>
        <th>Tipo</th>
        <th>Precio</th>
        <th>Descuento</th>
        <th>Capital</th>
        <th>Ganancia</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      @php
        $totalVenta = 0;
        $totalDescuento = 0;
        $totalCapital = 0;
        $totalGanancia = 0;
      @endphp

      @foreach($ventas as $venta)
        @foreach($venta->items as $item)
          @php
            $producto =
              $item->celular?->modelo ??
              $item->computadora?->nombre ??
              $item->productoGeneral?->nombre ??
              $item->productoApple?->modelo ??
              ($item->tipo === 'servicio' ? 'Servicio Técnico' : '—');

            $ganancia = $item->precio_venta - $item->descuento - $item->precio_invertido;

            $totalVenta += $item->precio_venta;
            $totalDescuento += $item->descuento;
            $totalCapital += $item->precio_invertido;
            $totalGanancia += $ganancia;
          @endphp

          <tr>
            <td>{{ $venta->nombre_cliente }}</td>
            <td>{{ $producto }}</td>
            <td>{{ ucfirst($item->tipo) }}</td>
            <td>Bs {{ number_format($item->precio_venta, 2) }}</td>
            <td>Bs {{ number_format($item->descuento, 2) }}</td>
            <td>Bs {{ number_format($item->precio_invertido, 2) }}</td>
            <td class="{{ $ganancia < 0 ? 'negativo' : 'positivo' }}">
              {{ $ganancia < 0
                ? 'Se invirtió Bs ' . number_format(abs($ganancia), 2)
                : 'Bs ' . number_format($ganancia, 2)
              }}
            </td>
            <td>{{ \Carbon\Carbon::parse($venta->created_at)->format('d/m/Y H:i') }}</td>
          </tr>
        @endforeach
      @endforeach
    </tbody>
  </table>

  <!-- RESUMEN -->
  <table class="resumen">
    <tr>
      <td>Total Vendido:</td>
      <td>Bs {{ number_format($totalVenta, 2) }}</td>
    </tr>
    <tr>
      <td>Total Descuentos:</td>
      <td>Bs {{ number_format($totalDescuento, 2) }}</td>
    </tr>
    <tr>
      <td>Total Capital:</td>
      <td>Bs {{ number_format($totalCapital, 2) }}</td>
    </tr>
    <tr>
      <td><strong>Ganancia Total:</strong></td>
      <td><strong>Bs {{ number_format($totalGanancia, 2) }}</strong></td>
    </tr>
  </table>

  <!-- FOOTER -->
  <footer>
    <div class="footer-left">
      <p>📞 +591 77 411 048</p>
      <p>📍 Av. Gualberto Villarroel entre Av. América y Calle Buenos Aires</p>
      <p>Cochabamba – Bolivia</p>
    </div>
    <div class="footer-right">
      <p>
        <strong>Empresa:</strong> Apple Technology<br>
        Documento interno válido solo con firma autorizada
      </p>
    </div>
  </footer>

</body>
</html>
