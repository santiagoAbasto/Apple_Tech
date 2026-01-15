<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <style>
    @page {
      margin: 30px 28px;
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 10.5px;
      color: #1e1e1e;
    }

    .header-wrap {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 8px;
      margin-bottom: 14px;
    }

    .brand img {
      width: 135px;
    }

    .company-name {
      position: absolute;
      top: 32px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      letter-spacing: 1px;
      color: #0f172a;
    }

    .company-sub {
      text-align: center;
      font-size: 9.5px;
      color: #475569;
      margin-top: 2px;
    }

    .venta-info {
      text-align: right;
      font-size: 10px;
    }

    .venta-info p {
      margin: 2px 0;
    }

    .section-title {
      font-size: 12px;
      font-weight: bold;
      color: #0f172a;
      margin-top: 16px;
      margin-bottom: 6px;
      border-bottom: 1px solid #0f172a;
      padding-bottom: 3px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 10px;
    }

    th {
      background: #f1f5f9;
      padding: 6px;
      border: 1px solid #cbd5e1;
      text-align: center;
    }

    td {
      padding: 6px;
      border: 1px solid #cbd5e1;
      vertical-align: middle;
    }

    .table-right {
      text-align: right;
    }

    .resumen {
      margin-top: 18px;
      font-size: 11px;
    }

    .resumen td {
      padding: 5px;
    }

    .resumen tr td:first-child {
      text-align: right;
      font-weight: bold;
      width: 80%;
    }

    .resumen tr td:last-child {
      text-align: right;
      width: 20%;
    }

    .notas {
      margin-top: 14px;
      font-size: 10px;
      border-left: 4px solid #0f172a;
      padding-left: 10px;
    }

    .firmas {
      margin-top: 40px;
      width: 100%;
      text-align: center;
    }

    .footer {
      margin-top: 20px;
      text-align: center;
      font-size: 9.5px;
      color: #475569;
    }

    .whatsapp {
      margin-top: 6px;
      font-size: 10.5px;
      font-weight: bold;
      color: #065f46;
    }
  </style>
</head>

<body>

  @php
  $items = collect($cotizacion->items ?? []);

  $telefono = $cotizacion->telefono ?? '—';
  $correo = $cotizacion->correo_cliente ?? '—';

  $subtotalSinFactura = 0;
  $ivaTotal = 0;
  $itTotal = 0;
  $subtotalConFactura = 0;

  foreach ($items as $i) {
  $cantidad = $i['cantidad'] ?? 1;
  $base = ($i['precio_sin_factura'] ?? 0) * $cantidad;

  $iva = $base * 0.13;
  $it = $base * 0.03;

  $subtotalSinFactura += $base;
  $ivaTotal += $iva;
  $itTotal += $it;
  $subtotalConFactura += ($base + $iva + $it);
  }

  $descuento = $cotizacion->descuento ?? 0;
  $totalFinal = max(0, $subtotalConFactura - $descuento);
  @endphp

  <!-- HEADER -->
  <div class="header-wrap">
    <div class="brand">
      <img src="{{ public_path('images/logo-appletech.jpeg') }}">
    </div>

    <div class="venta-info">
      <p><strong>COTIZACIÓN</strong></p>
      <p>Fecha: {{ optional($cotizacion->created_at)->format('d/m/Y H:i') }}</p>
      <p>N° Cotización: #{{ $cotizacion->id }}</p>
    </div>
  </div>

  <div class="company-name">APPLE TECHNOLOGY</div>
  <div class="company-sub">
    Av. Gualberto Villarroel entre Av. América y Calle Buenos Aires<br>
    Cochabamba – Bolivia
  </div>

  <!-- CLIENTE -->
  <div class="section-title">Datos del Cliente</div>
  <p><strong>Cliente:</strong> {{ $cotizacion->nombre_cliente }}</p>
  <p><strong>Teléfono:</strong> {{ $telefono }}</p>
  <p><strong>Correo:</strong> {{ $correo }}</p>
  <p><strong>Vendedor:</strong> {{ optional($cotizacion->usuario)->name ?? '—' }}</p>

  <!-- ITEMS -->
  <div class="section-title">Detalle de Productos</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th class="table-right">Precio S/F</th>
        <th class="table-right">IVA 13%</th>
        <th class="table-right">IT 3%</th>
        <th class="table-right">Precio C/F</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $i => $item)
      @php
      $cant = $item['cantidad'] ?? 1;
      $baseUnit = $item['precio_sin_factura'] ?? 0;
      $base = $baseUnit * $cant;
      $iva = $base * 0.13;
      $it = $base * 0.03;
      $totalLinea = $base + $iva + $it;
      @endphp
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $item['nombre'] ?? '—' }}</td>
        <td class="table-right">{{ $cant }}</td>
        <td class="table-right">Bs {{ number_format($base,2) }}</td>
        <td class="table-right">Bs {{ number_format($iva,2) }}</td>
        <td class="table-right">Bs {{ number_format($it,2) }}</td>
        <td class="table-right"><strong>Bs {{ number_format($totalLinea,2) }}</strong></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- RESUMEN -->
  <table class="resumen">
    <tr>
      <td>Subtotal SIN factura:</td>
      <td>Bs {{ number_format($subtotalSinFactura,2) }}</td>
    </tr>
    <tr>
      <td>IVA 13%:</td>
      <td>Bs {{ number_format($ivaTotal,2) }}</td>
    </tr>
    <tr>
      <td>IT 3%:</td>
      <td>Bs {{ number_format($itTotal,2) }}</td>
    </tr>
    <tr>
      <td>Subtotal CON factura:</td>
      <td>Bs {{ number_format($subtotalConFactura,2) }}</td>
    </tr>
    @if($descuento > 0)
    <tr>
      <td>Descuento:</td>
      <td>- Bs {{ number_format($descuento,2) }}</td>
    </tr>
    @endif
    <tr>
      <td><strong>TOTAL A PAGAR:</strong></td>
      <td><strong>Bs {{ number_format($totalFinal,2) }}</strong></td>
    </tr>
  </table>

  @if($cotizacion->notas_adicionales)
  <div class="notas">
    <strong>Notas:</strong> {{ $cotizacion->notas_adicionales }}
  </div>
  @endif

  <!-- FIRMAS -->
  <table class="firmas">
    <tr>
      <td>
        <img src="{{ public_path('images/firma.png') }}" width="140"><br>
        <strong>Firma autorizada</strong>
      </td>
      <td>
        <strong>Firma del Cliente</strong>
      </td>
    </tr>
  </table>

  <div class="footer">
    Documento sin valor fiscal · Cotización referencial<br>
    <div class="whatsapp">WhatsApp: +591 77 411 048</div>
  </div>

</body>

</html>