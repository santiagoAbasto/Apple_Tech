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

    .info p {
      margin: 2px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 10px;
    }

    th {
      background: #f1f5f9;
      color: #0f172a;
      padding: 6px;
      border: 1px solid #cbd5e1;
      text-align: center;
    }

    td {
      padding: 6px;
      border: 1px solid #cbd5e1;
      vertical-align: top;
    }

    .table-right {
      text-align: right;
    }

    .estado-imei {
      font-weight: 600;
      font-size: 9.5px;
      color: #0f172a;
      text-align: center;
      line-height: 1.2;
      word-break: break-word;
    }

    .resumen {
      margin-top: 18px;
      font-size: 11px;
    }

    .resumen td {
      padding: 4px 6px;
    }

    .resumen tr td:first-child {
      text-align: right;
      font-weight: bold;
      width: 85%;
    }

    .resumen tr td:last-child {
      text-align: right;
      width: 15%;
      color: #0f172a;
    }

    .notas {
      margin-top: 14px;
      font-size: 10px;
      border-left: 4px solid #0f172a;
      padding-left: 10px;
    }

    .firmas {
      margin-top: 45px;
      width: 100%;
      text-align: center;
    }

    .firmas td {
      width: 50%;
      height: 90px;
      vertical-align: bottom;
    }

    .firmas img {
      width: 150px;
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

  <!-- HEADER -->
  <div class="header-wrap">
    <div class="brand">
      <img src="{{ public_path('images/logo-appletech.jpeg') }}" alt="Apple Technology">
    </div>

    <div class="venta-info">
      <p><strong>BOLETA DE VENTA</strong></p>
      <p>Fecha: {{ optional($venta->created_at)->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
      <p>ID Venta: #{{ $venta->id }}</p>
      <p>Código Nota: {{ $venta->codigo_nota ?? '—' }}</p>
    </div>
  </div>

  <div class="company-name">APPLE TECHNOLOGY</div>
  <div class="company-sub">
    Av. Gualberto Villarroel entre Av. América y Calle Buenos Aires<br>
    Cochabamba – Bolivia
  </div>

  <!-- CLIENTE -->
  <div class="section-title">Datos del Cliente</div>
  <div class="info">
    <p><strong>Cliente:</strong> {{ $venta->nombre_cliente }}</p>
    <p><strong>Teléfono:</strong> {{ $venta->telefono_cliente ?? '—' }}</p>
    <p><strong>Método de pago:</strong> {{ ucfirst($venta->metodo_pago) }}</p>
    <p><strong>Vendedor:</strong> {{ $venta->vendedor->name ?? '—' }}</p>
  </div>

  @php
  $celulares = $venta->items->where('tipo','celular');
  $computadoras = $venta->items->where('tipo','computadora');
  $productosApple = $venta->items->where('tipo','producto_apple');
  $generales = $venta->items->where('tipo','producto_general');
  @endphp

  {{-- CELULARES --}}
  @if($celulares->count())
  <div class="section-title">Celulares Vendidos</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Modelo</th>
        <th>Capacidad</th>
        <th>Color</th>
        <th>IMEI 1</th>
        <th>IMEI 2</th>
        <th>Batería</th>
        <th>Estado IMEI</th>
        <th class="table-right">Precio</th>
        <th class="table-right">Descuento</th>
        <th class="table-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($celulares as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $item->celular->modelo }}</td>
        <td>{{ $item->celular->capacidad }}</td>
        <td>{{ $item->celular->color }}</td>
        <td>{{ $item->celular->imei_1 }}</td>
        <td>{{ $item->celular->imei_2 }}</td>
        <td>{{ $item->celular->bateria }}</td>
        <td class="estado-imei">{{ $item->celular->estado_imei }}</td>
        <td class="table-right">Bs {{ number_format($item->precio_venta,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->descuento,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- COMPUTADORAS --}}
  @if($computadoras->count())
  <div class="section-title">Computadoras Vendidas</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Procesador</th>
        <th>RAM</th>
        <th>Almacenamiento</th>
        <th>Batería</th>
        <th>Color</th>
        <th>Serie</th>
        <th class="table-right">Precio</th>
        <th class="table-right">Descuento</th>
        <th class="table-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($computadoras as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $item->computadora->nombre }}</td>
        <td>{{ $item->computadora->procesador }}</td>
        <td>{{ $item->computadora->ram }}</td>
        <td>{{ $item->computadora->almacenamiento }}</td>
        <td>{{ $item->computadora->bateria }}</td>
        <td>{{ $item->computadora->color }}</td>
        <td>{{ $item->computadora->numero_serie }}</td>
        <td class="table-right">Bs {{ number_format($item->precio_venta,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->descuento,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- PRODUCTOS APPLE --}}
  @if($productosApple->count())
  <div class="section-title">Productos Apple Vendidos</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Modelo</th>
        <th>Capacidad</th>
        <th>Batería</th>
        <th>Color</th>
        <th>Serie / IMEI</th>
        <th>Tiene IMEI</th>
        <th>Estado IMEI</th>
        <th class="table-right">Precio</th>
        <th class="table-right">Descuento</th>
        <th class="table-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($productosApple as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $item->productoApple->modelo }}</td>
        <td>{{ $item->productoApple->capacidad }}</td>
        <td>{{ $item->productoApple->bateria }}</td>
        <td>{{ $item->productoApple->color }}</td>
        <td>
          @if($item->productoApple->tiene_imei)
          IMEI 1: {{ $item->productoApple->imei_1 }}<br>
          IMEI 2: {{ $item->productoApple->imei_2 }}
          @else
          {{ $item->productoApple->numero_serie }}
          @endif
        </td>
        <td>{{ $item->productoApple->tiene_imei ? 'Sí' : 'No' }}</td>
        <td class="estado-imei">{{ $item->productoApple->estado_imei ?? '—' }}</td>
        <td class="table-right">Bs {{ number_format($item->precio_venta,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->descuento,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- PRODUCTOS GENERALES --}}
  @if($generales->count())
  <div class="section-title">Productos Generales Vendidos</div>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Código</th>
        <th class="table-right">Precio</th>
        <th class="table-right">Descuento</th>
        <th class="table-right">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      @foreach($generales as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $item->productoGeneral->nombre }}</td>
        <td>{{ $item->productoGeneral->tipo }}</td>
        <td>{{ $item->productoGeneral->codigo }}</td>
        <td class="table-right">Bs {{ number_format($item->precio_venta,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->descuento,2) }}</td>
        <td class="table-right">Bs {{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @endif

  {{-- PERMUTA --}}
  @if ($venta->entregadoCelular || $venta->entregadoComputadora || $venta->entregadoProductoGeneral || $venta->entregadoProductoApple)
  <div class="section-title">Producto Entregado en Permuta</div>

  @if($venta->entregadoCelular)
  <table>
    <thead>
      <tr>
        <th>Modelo</th>
        <th>Capacidad</th>
        <th>Color</th>
        <th>IMEI 1</th>
        <th>IMEI 2</th>
        <th>Batería</th>
        <th>Estado IMEI</th>
        <th class="table-right">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $venta->entregadoCelular->modelo }}</td>
        <td>{{ $venta->entregadoCelular->capacidad }}</td>
        <td>{{ $venta->entregadoCelular->color }}</td>
        <td>{{ $venta->entregadoCelular->imei_1 }}</td>
        <td>{{ $venta->entregadoCelular->imei_2 }}</td>
        <td>{{ $venta->entregadoCelular->bateria }}</td>
        <td class="estado-imei">{{ $venta->entregadoCelular->estado_imei }}</td>
        <td class="table-right">Bs {{ number_format($venta->entregadoCelular->precio_costo,2) }}</td>
      </tr>
    </tbody>
  </table>
  @endif

  @if($venta->entregadoComputadora)
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Procesador</th>
        <th>RAM</th>
        <th>Almacenamiento</th>
        <th>Batería</th>
        <th>Color</th>
        <th>Serie</th>
        <th class="table-right">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $venta->entregadoComputadora->nombre }}</td>
        <td>{{ $venta->entregadoComputadora->procesador }}</td>
        <td>{{ $venta->entregadoComputadora->ram }}</td>
        <td>{{ $venta->entregadoComputadora->almacenamiento }}</td>
        <td>{{ $venta->entregadoComputadora->bateria }}</td>
        <td>{{ $venta->entregadoComputadora->color }}</td>
        <td>{{ $venta->entregadoComputadora->numero_serie }}</td>
        <td class="table-right">Bs {{ number_format($venta->entregadoComputadora->precio_costo,2) }}</td>
      </tr>
    </tbody>
  </table>
  @endif

  @if($venta->entregadoProductoGeneral)
  <table>
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Tipo</th>
        <th>Código</th>
        <th class="table-right">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $venta->entregadoProductoGeneral->nombre }}</td>
        <td>{{ $venta->entregadoProductoGeneral->tipo }}</td>
        <td>{{ $venta->entregadoProductoGeneral->codigo }}</td>
        <td class="table-right">Bs {{ number_format($venta->entregadoProductoGeneral->precio_costo,2) }}</td>
      </tr>
    </tbody>
  </table>
  @endif

  @if($venta->entregadoProductoApple)
  <table>
    <thead>
      <tr>
        <th>Modelo</th>
        <th>Capacidad</th>
        <th>Batería</th>
        <th>Color</th>
        <th>IMEI 1</th>
        <th>IMEI 2</th>
        <th>Serie</th>
        <th>Estado IMEI</th>
        <th class="table-right">Valor</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $venta->entregadoProductoApple->modelo }}</td>
        <td>{{ $venta->entregadoProductoApple->capacidad }}</td>
        <td>{{ $venta->entregadoProductoApple->bateria }}</td>
        <td>{{ $venta->entregadoProductoApple->color }}</td>
        <td>{{ $venta->entregadoProductoApple->imei_1 }}</td>
        <td>{{ $venta->entregadoProductoApple->imei_2 }}</td>
        <td>{{ $venta->entregadoProductoApple->numero_serie }}</td>
        <td class="estado-imei">{{ $venta->entregadoProductoApple->estado_imei }}</td>
        <td class="table-right">Bs {{ number_format($venta->entregadoProductoApple->precio_costo,2) }}</td>
      </tr>
    </tbody>
  </table>
  @endif

  @endif

  {{-- RESUMEN --}}
  @php
  $subtotal = $venta->items->sum('subtotal');
  $permuta = $venta->valor_permuta ?? 0;
  $total = $subtotal - $permuta;
  @endphp

  <table class="resumen">
    <tr>
      <td>Subtotal:</td>
      <td>Bs {{ number_format($subtotal,2) }}</td>
    </tr>
    @if($permuta > 0)
    <tr>
      <td>Valor producto en permuta:</td>
      <td>- Bs {{ number_format($permuta,2) }}</td>
    </tr>
    @endif
    <tr>
      <td><strong>Total a pagar:</strong></td>
      <td><strong>Bs {{ number_format($total,2) }}</strong></td>
    </tr>
  </table>

  @if($venta->notas_adicionales)
  <div class="notas">
    <strong>Notas:</strong> {{ $venta->notas_adicionales }}
  </div>
  @endif

  <table class="firmas" style="margin-top:40px;">
    <tr>
      <!-- FIRMA EMPRESA -->
      <td style="width:50%; text-align:center; vertical-align:bottom; height:90px;">
        <img src="{{ public_path('images/firma.png') }}" style="width:150px; opacity:0.95;"><br>
        <strong>Firma autorizada</strong><br>
        Apple Technology
      </td>

      <!-- FIRMA CLIENTE -->
      <td style="width:50%; text-align:center; vertical-align:bottom; height:90px;">
        <strong>Firma del Cliente</strong><br>
        <span style="font-size:9px;color:#555;">
          Conforme con la recepción del producto
        </span>
      </td>
    </tr>
  </table>

  <div class="footer">
    Documento sin valor fiscal · Garantía según condiciones del servicio<br>
    <div class="whatsapp"> WhatsApp: +591 77 411 048</div>
  </div>

</body>

</html>