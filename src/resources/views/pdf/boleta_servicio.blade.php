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

    /* =====================
       HEADER
    ====================== */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 2px solid #003366;
      padding-bottom: 8px;
      margin-bottom: 14px;
    }

    .brand img {
      width: 120px;
    }

    .empresa-legal p {
      margin: 2px 0;
      font-size: 9.5px;
    }

    .boleta-info {
      text-align: right;
    }

    .boleta-info .titulo {
      font-size: 13px;
      font-weight: bold;
      color: #003366;
      margin-bottom: 4px;
    }

    /* =====================
       SECCIONES
    ====================== */
    .section-title {
      font-size: 11.5px;
      font-weight: bold;
      color: #003366;
      border-bottom: 1px solid #003366;
      padding-bottom: 3px;
      margin-top: 14px;
      margin-bottom: 6px;
    }

    .info p {
      margin: 2px 0;
    }

    /* =====================
       TABLAS
    ====================== */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
      font-size: 10px;
    }

    th,
    td {
      border: 1px solid #d0dce7;
      padding: 6px;
      text-align: center;
    }

    th {
      background-color: #e9f0fa;
      color: #003366;
      font-weight: bold;
    }

    td.text-left {
      text-align: left;
    }

    /* =====================
       RESUMEN
    ====================== */
    .resumen {
      margin-top: 16px;
      text-align: right;
      font-size: 13px;
      font-weight: bold;
      color: #003366;
    }

    /* =====================
       FIRMAS
    ====================== */
    .tabla-firmas {
      width: 100%;
      margin-top: 50px;
      border-collapse: collapse;
    }

    .firma-box {
      border: 1px solid #cfcfcf;
      height: 150px;
      text-align: center;
      padding: 18px 10px;
    }

    .firma-img {
      width: 160px;
      margin-bottom: 12px;
    }

    .firma-line {
      border-top: 1px solid #777;
      width: 80%;
      margin: 6px auto;
    }

    .firma-text {
      font-size: 9.5px;
      color: #444;
    }
  </style>
</head>

<body>

  <!-- HEADER -->
  <div class="header">
    <div class="brand">
      <img src="{{ public_path('images/LOGO.png') }}" alt="Apple Boss">
      <div class="empresa-legal">
        <p><strong>NIT:</strong> 12555473014</p>
        <p><strong>Contribuyente:</strong> Empresa Unipersonal</p>
      </div>
    </div>

    <div class="boleta-info">
      <div class="titulo">BOLETA DE SERVICIO TÉCNICO</div>
      <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($servicio->fecha)->format('d/m/Y') }}</p>
      <p><strong>Código Nota:</strong> {{ $servicio->codigo_nota ?? '—' }}</p>
    </div>
  </div>

  <!-- DATOS CLIENTE -->
  <div class="section-title">Datos del Cliente</div>
  <div class="info">
    <p><strong>Cliente:</strong> {{ strtoupper($servicio->cliente) }}</p>
    <p><strong>Teléfono:</strong> {{ $servicio->telefono ?? '—' }}</p>
    <p><strong>Equipo:</strong> {{ $servicio->equipo }}</p>
    <p><strong>Técnico:</strong> {{ $servicio->tecnico }}</p>
  </div>

  <!-- DETALLE -->
  <div class="section-title">Detalle del Servicio</div>

  <table>
    <thead>
      <tr>
        <th style="width:5%">#</th>
        <th style="width:70%">Servicio realizado</th>
        <th style="width:25%">Precio (Bs)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($servicios_cliente as $i => $item)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td class="text-left">{{ $item['descripcion'] }}</td>
        <td>
          Bs {{ number_format($item['precio'] ?? 0, 2) }}
        </td>

      </tr>
      @endforeach
    </tbody>

  </table>

  <!-- TOTAL -->
  <div class="resumen">
    Total a pagar por el cliente: <br>
    <span style="font-size:18px;">Bs {{ number_format($servicio->precio_venta, 2) }}</span>
  </div>

  <!-- FIRMAS -->
  <table class="tabla-firmas">
    <tr>
      <td style="width:50%; padding-right:10px;">
        <div class="firma-box">
          <img src="{{ public_path('images/firma.png') }}" class="firma-img" alt="Firma Apple Boss">
          <div class="firma-line"></div>
          <div class="firma-text">
            <strong>Firma autorizada</strong><br>
            Apple Boss
          </div>
        </div>
      </td>

      <td style="width:50%; padding-left:10px;">
        <div class="firma-box">
          <div style="height:127px;"></div>
          <div class="firma-line"></div>
          <div class="firma-text">
            <strong>Firma del Cliente</strong><br>
            Conforme con la recepción del servicio
          </div>
        </div>
      </td>
    </tr>
  </table>

</body>

</html>