<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">

  <style>
    @page {
      margin: 6px;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: #000;
    }

    /* UTILIDADES */
    .center {
      text-align: center;
    }

    .right {
      text-align: right;
    }

    .bold {
      font-weight: bold;
    }

    /* LOGO */
    .logo {
      text-align: center;
      margin-bottom: 6px;
    }

    .logo img {
      width: 130px;
    }

    /* TITULOS */
    .title-main {
      font-size: 14px;
      font-weight: bold;
      text-align: center;
      margin: 4px 0;
    }

    .title-section {
      font-size: 11px;
      font-weight: bold;
      margin-top: 6px;
      margin-bottom: 2px;
      text-transform: uppercase;
    }

    /* SUBTEXTO */
    .subtitle {
      font-size: 10px;
      text-align: center;
      line-height: 1.4;
    }

    /* DIVISORES */
    .divider {
      border-top: 2px dashed #000;
      margin: 8px 0;
    }

    /* INFO */
    .info p {
      margin: 2px 0;
    }

    /* TABLA */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
    }

    thead th {
      font-size: 10px;
      font-weight: bold;
      border-bottom: 1px solid #000;
      padding-bottom: 4px;
      text-align: left;
    }

    td {
      font-size: 10px;
      padding: 4px 0;
    }

    td:last-child,
    th:last-child {
      text-align: right;
    }

    /* TOTAL */
    .total-box {
      margin-top: 8px;
      padding: 6px 0;
      border-top: 2px solid #000;
      border-bottom: 2px solid #000;
    }

    .total {
      font-size: 14px;
      font-weight: bold;
    }

    /* NOTAS */
    .notas {
      margin-top: 6px;
      font-size: 10px;
    }

    /* FOOTER */
    .footer {
      margin-top: 10px;
      text-align: center;
      font-size: 9px;
      line-height: 1.4;
    }
  </style>
</head>

<body>

  <!-- LOGO -->
  <div class="logo">
    <img src="{{ public_path('images/logo-appletech.jpeg') }}">
  </div>

  <!-- EMPRESA -->
  <div class="center">
    <div class="title-main">APPLE TECHNOLOGY</div>
    <div class="subtitle">
      Av. Gualberto Villarroel<br>
      entre Av. América y Calle Buenos Aires<br>
      Cochabamba – Bolivia
    </div>
  </div>

  <div class="divider"></div>

  <!-- RECIBO -->
  <div class="center title-section">RECIBO DE SERVICIO TÉCNICO</div>

  <div class="info">
    <p>Fecha: {{ optional($servicio->created_at)->timezone(config('app.timezone'))->format('d/m/Y H:i') }}</p>
    <p><strong>Nº Nota:</strong> {{ $servicio->codigo_nota }}</p>
  </div>

  <div class="divider"></div>

  <!-- CLIENTE -->
  <div class="title-section">Datos del Cliente</div>
  <div class="info">
    <p><strong>Cliente:</strong> {{ $servicio->cliente }}</p>
    <p><strong>Teléfono:</strong> {{ $servicio->telefono ?? '—' }}</p>
    <p><strong>Equipo:</strong> {{ $servicio->equipo }}</p>
    <p><strong>Técnico:</strong> {{ $servicio->tecnico }}</p>
  </div>

  <div class="divider"></div>

  <!-- DETALLE -->
  <div class="title-section">Detalle del Servicio</div>

  <table>
    <thead>
      <tr>
        <th>Descripción</th>
        <th>Bs</th>
      </tr>
    </thead>
    <tbody>
      @foreach($servicios_cliente as $item)
      <tr>
        <td>{{ $item['descripcion'] }}</td>
        <td>{{ number_format($item['precio'], 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- TOTAL -->
  <div class="total-box right">
    <div>TOTAL A PAGAR</div>
    <div class="total">Bs {{ number_format($servicio->precio_venta, 2) }}</div>
  </div>

  <!-- NOTAS -->
  @if(!empty($servicio->notas_adicionales))
  <div class="notas">
    <div class="title-section">Notas adicionales</div>
    {{ $servicio->notas_adicionales }}
  </div>
  @endif

  <div class="divider"></div>

  <!-- FOOTER -->
  <div class="footer">
    Documento interno sin valor fiscal<br>
    Servicio técnico garantizado<br>
    Gracias por su preferencia
  </div>

</body>

</html>