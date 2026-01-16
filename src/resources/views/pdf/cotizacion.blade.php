<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <style>
    @page {
      margin: 28px;
    }

    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 11px;
      color: #1e293b;
    }

    .header {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px solid #0f172a;
      padding-bottom: 10px;
      margin-bottom: 14px;
    }

    .logo img {
      width: 130px;
    }

    .doc-info {
      text-align: right;
      font-size: 10px;
    }

    .doc-info strong {
      font-size: 13px;
      color: #0f172a;
    }

    .company {
      text-align: center;
      margin-bottom: 16px;
    }

    .company h1 {
      font-size: 18px;
      margin: 4px 0;
      letter-spacing: 1px;
    }

    .company p {
      font-size: 9.5px;
      color: #64748b;
      margin: 0;
    }

    .section-title {
      font-size: 12px;
      font-weight: bold;
      border-bottom: 1px solid #cbd5e1;
      padding-bottom: 4px;
      margin-top: 18px;
      margin-bottom: 8px;
    }

    .box {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      padding: 10px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 10px;
      margin-top: 6px;
    }

    th,
    td {
      border: 1px solid #cbd5e1;
      padding: 6px;
    }

    th {
      background: #f1f5f9;
      text-align: center;
    }

    .right {
      text-align: right;
    }

    .total-final {
      background: #0f172a;
      color: white;
      font-size: 14px;
      font-weight: bold;
      text-align: center;
    }

    .footer {
      margin-top: 20px;
      text-align: center;
      font-size: 9.5px;
      color: #64748b;
    }

    .whatsapp {
      font-weight: bold;
      color: #047857;
    }
  </style>
</head>

<body>

  @php
  // 1) Normalizar items (puede venir como JSON string, array o stdClass)
  $rawItems = $cotizacion->items ?? [];

  if (is_string($rawItems)) {
  $decoded = json_decode($rawItems, true);
  $items = is_array($decoded) ? $decoded : [];
  } elseif ($rawItems instanceof \Illuminate\Support\Collection) {
  $items = $rawItems->toArray();
  } elseif (is_array($rawItems)) {
  $items = $rawItems;
  } else {
  // stdClass o cualquier cosa rara
  $items = (array) $rawItems;
  }

  // 2) Helper numérico seguro
  $num = function ($v) {
  if ($v === null) return 0;
  if (is_bool($v)) return $v ? 1 : 0;
  if (is_numeric($v)) return (float) $v;

  // strings tipo "Bs 2.900,00"
  if (is_string($v)) {
  $s = preg_replace('/[^0-9,.\-]/', '', $v);
  // si tiene coma como decimal
  if (substr_count($s, ',') === 1 && substr_count($s, '.') >= 1) {
  $s = str_replace('.', '', $s);
  $s = str_replace(',', '.', $s);
  } elseif (substr_count($s, ',') === 1 && substr_count($s, '.') === 0) {
  $s = str_replace(',', '.', $s);
  }
  return is_numeric($s) ? (float) $s : 0;
  }

  return 0;
  };

  // 3) Totales igual que tu frontend
  $subtotalSF = 0; // sum(precio_sin_factura * cantidad)
  $descuentos = 0; // sum(descuento)
  $ivaTotal = 0; // sum( (base-desc)*0.13 )
  $itTotal = 0; // sum( (base-desc)*0.03 )
  $totalFinal = 0; // sum( neto + iva + it )

  $telefono = $cotizacion->telefono_completo
  ?? $cotizacion->telefono
  ?? $cotizacion->telefono_cliente
  ?? '—';
  @endphp

  <!-- HEADER -->
  <div class="header">
    <div class="logo">
      <img src="{{ public_path('images/logo-appletech.jpeg') }}">
    </div>
    <div class="doc-info">
      <strong>COTIZACIÓN</strong><br>
      Fecha: {{ optional($cotizacion->created_at)->format('d/m/Y H:i') }}<br>
      Nº #{{ $cotizacion->id }}
    </div>
  </div>

  <div class="company">
    <h1>APPLE TECHNOLOGY</h1>
    <p>Av. Gualberto Villarroel entre Av. América y Calle Buenos Aires · Cochabamba</p>
  </div>

  <!-- CLIENTE -->
  <div class="section-title">Datos del Cliente</div>
  <div class="box">
    <strong>Cliente:</strong> {{ $cotizacion->nombre_cliente ?? '—' }}<br>
    <strong>Teléfono:</strong> {{ $telefono }}<br>
    <strong>Correo:</strong> {{ $cotizacion->correo_cliente ?? '—' }}<br>
  </div>

  <!-- DETALLE -->
  <div class="section-title">Detalle de Productos</div>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Producto</th>
        <th>Cant.</th>
        <th class="right">Precio Base</th>
        <th class="right">Descuento</th>
        <th class="right">IVA 13%</th>
        <th class="right">IT 3%</th>
        <th class="right">Total</th>
      </tr>
    </thead>

    <tbody>
      @forelse($items as $i => $it)
      @php
      // it puede ser array o stdClass
      $itArr = is_array($it) ? $it : (array) $it;

      $nombre = $itArr['nombre'] ?? '—';
      $cantidad = max(1, (int) $num($itArr['cantidad'] ?? 1));
      $precioSF = $num($itArr['precio_sin_factura'] ?? 0);
      $descRaw = $num($itArr['descuento'] ?? 0);

      $base = $precioSF * $cantidad;
      $descuentoItem = min(max(0, $descRaw), $base); // clamp 0..base
      $baseNeta = max(0, $base - $descuentoItem);

      // EXACTO igual que tu Create.jsx
      $iva = round($baseNeta * 0.13, 2);
      $itx = round($baseNeta * 0.03, 2);
      $total = round($baseNeta + $iva + $itx, 2);

      $subtotalSF += $base;
      $descuentos += $descuentoItem;
      $ivaTotal += $iva;
      $itTotal += $itx;
      $totalFinal += $total;
      @endphp

      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $nombre }}</td>
        <td class="right">{{ $cantidad }}</td>
        <td class="right">Bs {{ number_format($base, 2) }}</td>
        <td class="right">- Bs {{ number_format($descuentoItem, 2) }}</td>
        <td class="right">Bs {{ number_format($iva, 2) }}</td>
        <td class="right">Bs {{ number_format($itx, 2) }}</td>
        <td class="right"><strong>Bs {{ number_format($total, 2) }}</strong></td>
      </tr>
      @empty
      <tr>
        <td colspan="8" class="right" style="text-align:center; color:#64748b;">
          No hay ítems agregados
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <!-- RESUMEN -->
  <div class="section-title">Resumen Final</div>

  <table>
    <tr>
      <td class="right"><strong>Importe neto sin factura:</strong></td>
      <td class="right">
        Bs {{ number_format($subtotalSF - $descuentos, 2) }}
      </td>
    </tr>

    <tr class="total-final">
      <td>IMPORTE NETO CON FACTURA</td>
      <td>Bs {{ number_format($totalFinal, 2) }}</td>
    </tr>
  </table>

  <!-- NOTAS -->
  @if(!empty($cotizacion->notas_adicionales))
  <div class="section-title">Notas adicionales</div>
  <div class="box">
    {{ $cotizacion->notas_adicionales }}
  </div>
  @endif

  <div class="footer">
    Documento sin valor fiscal · Cotización referencial<br>
    <div class="whatsapp">WhatsApp: +591 77 411 048</div>
  </div>

</body>

</html>