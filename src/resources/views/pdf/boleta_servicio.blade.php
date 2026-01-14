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
      text-align: left;
    }

    td {
      padding: 6px;
      border: 1px solid #cbd5e1;
    }

    th:last-child,
    td:last-child {
      text-align: right;
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
      <p><strong>BOLETA DE SERVICIO TÉCNICO</strong></p>
      <p>Fecha: {{ optional($servicio->created_at)->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
      <p>ID Servicio: #{{ $servicio->id }}</p>
      <p>Código Nota: {{ $servicio->codigo_nota }}</p>
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
    <p><strong>Cliente:</strong> {{ $servicio->cliente }}</p>
    <p><strong>Teléfono:</strong> {{ $servicio->telefono ?? '—' }}</p>
    <p><strong>Equipo:</strong> {{ $servicio->equipo }}</p>
    <p><strong>Técnico:</strong> {{ $servicio->tecnico }}</p>
    <p><strong>Registrado por:</strong> {{ $servicio->vendedor->name ?? '—' }}</p>
  </div>

  <!-- DETALLE -->
  <div class="section-title">Detalle del Servicio</div>
  <table>
    <thead>
      <tr>
        <th>Descripción</th>
        <th>Importe (Bs)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($servicios_cliente as $item)
      <tr>
        <td>{{ $item['descripcion'] }}</td>
        <td>Bs {{ number_format($item['precio'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- RESUMEN -->
  <table class="resumen">
    <tr>
      <td><strong>Total a pagar:</strong></td>
      <td><strong>Bs {{ number_format($servicio->precio_venta, 2) }}</strong></td>
    </tr>
  </table>

  @if($servicio->notas_adicionales)
  <div class="notas">
    <strong>Notas:</strong> {{ $servicio->notas_adicionales }}
  </div>
  @endif

  <table class="firmas">
    <tr>
      <td>
        <img src="{{ public_path('images/firma.png') }}"><br>
        <strong>Firma autorizada</strong><br>
        Apple Technology
      </td>
      <td>
        <strong>Firma del Cliente</strong><br>
        <span style="font-size:9px;color:#555;">
          Conforme con la recepción del servicio
        </span>
      </td>
    </tr>
  </table>

  <div class="footer">
    Documento sin valor fiscal · Servicio técnico garantizado<br>
    <div class="whatsapp"> WhatsApp: +591 77 411 048</div>
  </div>

</body>
</html>
