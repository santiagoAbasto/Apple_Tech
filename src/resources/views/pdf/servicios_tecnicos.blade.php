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
    }

    .header-wrap {
      display: flex;
      justify-content: space-between;
      border-bottom: 2px solid #003366;
      margin-bottom: 10px;
    }

    .brand img {
      width: 130px;
    }

    .title-top {
      text-align: center;
      font-size: 20px;
      font-weight: bold;
      color: #003366;
      margin-top: -75px;
    }

    .venta-info {
      text-align: right;
      font-size: 10px;
    }

    .venta-info p {
      margin: 1px 0;
      color: #333;
    }

    .empresa-legal p {
      margin: 1px 0;
      font-size: 9.8px;
      color: #333;
    }

    .section-title {
      font-size: 12px;
      font-weight: bold;
      margin-top: 14px;
      margin-bottom: 6px;
      color: #003366;
      border-bottom: 1px solid #003366;
      padding-bottom: 3px;
    }

    .info p {
      margin: 1px 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 10px;
      text-align: center;
    }

    th, td {
      border: 1px solid #d0dce7;
      padding: 6px;
      vertical-align: middle;
    }

    th {
      background-color: #e9f0fa;
      color: #003366;
    }

    .resumen-final {
      margin-top: 20px;
      font-size: 12px;
      font-weight: bold;
      text-align: right;
      color: #003366;
    }

    .firmas {
      margin-top: 90px;
      width: 100%;
      font-size: 10.5px;
      text-align: center;
      border-collapse: collapse;
    }

    .firmas img {
      width: 150px;
      opacity: 0.95;
    }

    .firma-text {
      font-weight: bold;
      color: #003366;
      padding-top: 5px;
    }

    .firma-sub {
      font-size: 9px;
      color: #555;
      padding-top: 4px;
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

  <!-- LOGO -->
  <div class="brand">
    <img src="{{ public_path('images/logo-appletech.jpeg') }}" alt="Apple Technology">
  </div>

  <h1 class="title-top">APPLE TECHNOLOGY</h1>

  <!-- HEADER INFO -->
  <div class="header-wrap">
    <div class="empresa-legal">
      <p><strong>Empresa:</strong> Apple Technology</p>
      <p><strong>Gerente General:</strong> Edson Torrez Huallpa</p>
    </div>

    <div class="venta-info">
      <p><strong>BOLETA DE SERVICIO TÉCNICO</strong></p>
      <p>Fecha: {{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</p>
      <p>Código Nota: {{ $servicio->codigo_nota ?? '—' }}</p>
    </div>
  </div>

  <!-- CLIENTE -->
  <div class="section-title">Datos del Cliente</div>
  <div class="info">
    <p><strong>Cliente:</strong> {{ strtoupper($servicio->cliente) }}</p>
    <p><strong>Teléfono:</strong> {{ $servicio->telefono ?? '—' }}</p>
  </div>

  <!-- DETALLE -->
  <div class="section-title">Detalle del Servicio</div>
  <table>
    <thead>
      <tr>
        <th>Equipo</th>
        <th>Servicio</th>
        <th>Técnico</th>
        <th>Registrado por</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>{{ $servicio->equipo }}</td>
        <td>{{ $servicio->detalle_servicio }}</td>
        <td>{{ $servicio->tecnico }}</td>
        <td>{{ $servicio->vendedor->name ?? '—' }}</td>
      </tr>
    </tbody>
  </table>

  <!-- TOTAL -->
  <div class="resumen-final">
    Total a pagar por el cliente: Bs {{ number_format($servicio->precio_venta, 2) }}
  </div>

  <!-- FIRMAS -->
  <table class="firmas">
    <tr>
      <td style="width:50%; height:80px;">
        <img src="{{ public_path('images/firma.png') }}" alt="Firma Gerente">
      </td>
      <td style="width:50%; height:80px;"></td>
    </tr>
    <tr>
      <td class="firma-text">Firma autorizada – Apple Technology</td>
      <td class="firma-text">Firma del Cliente</td>
    </tr>
    <tr>
      <td></td>
      <td class="firma-sub">Conforme con la recepción del servicio</td>
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
      <p><strong>Validez:</strong><br>Documento interno válido solo con firma autorizada.</p>
    </div>
  </footer>

</body>
</html>
