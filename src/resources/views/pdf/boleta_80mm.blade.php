<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 6mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            color: #111827;
            margin: 0;
            padding: 0;
        }

        /* =====================
   HEADER
===================== */
        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .header img {
            width: 120px;
            margin-bottom: 4px;
        }

        .header .title {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .header .sub {
            font-size: 8px;
            color: #374151;
            line-height: 1.3;
        }

        /* =====================
   DIVIDER
===================== */
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        /* =====================
   INFO
===================== */
        .info p {
            margin: 1px 0;
        }

        /* =====================
   ITEMS
===================== */
        .item {
            margin-bottom: 6px;
        }

        .item .name {
            font-weight: bold;
        }

        .item .meta {
            font-size: 8px;
            color: #374151;
            line-height: 1.3;
        }

        .price-line {
            display: flex;
            justify-content: space-between;
            margin-top: 2px;
        }

        /* =====================
   TOTAL
===================== */
        .total {
            font-size: 12px;
            font-weight: bold;
            text-align: right;
            margin-top: 8px;
        }

        /* =====================
   FIRMAS
===================== */
        .firmas {
            margin-top: 28px;
            width: 100%;
            text-align: center;
        }

        .firma-box {
            width: 48%;
            display: inline-block;
            vertical-align: bottom;
        }

        .firma-box img {
            width: 120px;
        }

        .firma-box .label {
            font-size: 8px;
            margin-top: 4px;
        }

        body {
            width: 72mm;
            margin: 0;
            padding: 0;
        }

        html {
            width: 72mm;
        }


        /* =====================
                FOOTER
        ===================== */
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8px;
            color: #374151;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <img src="{{ public_path('images/logo-appletech.jpeg') }}" alt="Apple Technology">
        <div class="title">APPLE TECHNOLOGY</div>
        <div class="sub">
            Av. Gualberto Villarroel entre Av. América<br>
            y Calle Buenos Aires · Cochabamba<br>
            <strong> +591 77 411 048</strong>
        </div>
    </div>

    <div class="divider"></div>

    <!-- INFO VENTA -->
    <div class="info">
        <p>
            <strong>Fecha:</strong>
            {{ optional($venta->created_at)
        ->timezone(config('app.timezone'))
        ->format('d/m/Y H:i') }}
        </p>
        <p><strong>Nota:</strong> {{ $venta->codigo_nota }}</p>
        <p><strong>Cliente:</strong> {{ $venta->nombre_cliente }}</p>
        <p><strong>Tel:</strong> {{ $venta->telefono_cliente ?? '—' }}</p>
        <p><strong>Vendedor:</strong> {{ $venta->vendedor->name ?? '—' }}</p>
    </div>

    <div class="divider"></div>

    <!-- ITEMS -->
    @foreach($venta->items as $item)
    <div class="item">
        <div class="name">{{ strtoupper($item->tipo) }}</div>

        <div class="meta">
            @if($item->tipo === 'celular' && $item->celular)
            {{ $item->celular->modelo }} · {{ $item->celular->capacidad }} · {{ $item->celular->color }}<br>
            IMEI 1: {{ $item->celular->imei_1 }}<br>
            IMEI 2: {{ $item->celular->imei_2 }}<br>
            Batería: {{ $item->celular->bateria }} · Estado IMEI: {{ $item->celular->estado_imei }}
            @elseif($item->tipo === 'computadora' && $item->computadora)
            {{ $item->computadora->nombre }} · {{ $item->computadora->procesador }}<br>
            {{ $item->computadora->ram }} / {{ $item->computadora->almacenamiento }}
            @elseif($item->tipo === 'producto_apple' && $item->productoApple)
            {{ $item->productoApple->modelo }} · {{ $item->productoApple->capacidad }}<br>
            {{ $item->productoApple->color }} · Batería: {{ $item->productoApple->bateria }}
            @elseif($item->productoGeneral)
            {{ $item->productoGeneral->nombre }} · {{ $item->productoGeneral->codigo }}
            @endif
        </div>

        <div class="price-line">
            <span>Subtotal</span>
            <span>Bs {{ number_format($item->subtotal, 2) }}</span>
        </div>
    </div>
    @endforeach

    {{-- =====================
     PERMUTA DETALLADA
===================== --}}
    @if(
    $venta->entregadoCelular ||
    $venta->entregadoComputadora ||
    $venta->entregadoProductoApple
    )
    <div class="divider"></div>

    <div class="info">
        <p><strong>Producto entregado en permuta</strong></p>

        {{-- CELULAR --}}
        @if($venta->entregadoCelular)
        <p>
            <strong>Tipo:</strong> Celular<br>
            {{ $venta->entregadoCelular->modelo }} · {{ $venta->entregadoCelular->color }}<br>
            Batería: {{ $venta->entregadoCelular->bateria }}<br>
            Estado IMEI: {{ $venta->entregadoCelular->estado_imei }}<br>
            IMEI 1: {{ $venta->entregadoCelular->imei_1 }}<br>
            IMEI 2: {{ $venta->entregadoCelular->imei_2 }}
        </p>
        <p>
            <strong>Valor aplicado:</strong>
            - Bs {{ number_format($venta->entregadoCelular->precio_costo, 2) }}
        </p>
        @endif

        {{-- COMPUTADORA --}}
        @if($venta->entregadoComputadora)
        <p>
            <strong>Tipo:</strong> Computadora<br>
            {{ $venta->entregadoComputadora->nombre }}<br>
            {{ $venta->entregadoComputadora->procesador }} ·
            {{ $venta->entregadoComputadora->ram }} /
            {{ $venta->entregadoComputadora->almacenamiento }}<br>
            Serie: {{ $venta->entregadoComputadora->numero_serie }}
        </p>
        <p>
            <strong>Valor aplicado:</strong>
            - Bs {{ number_format($venta->entregadoComputadora->precio_costo, 2) }}
        </p>
        @endif

        {{-- PRODUCTO APPLE --}}
        @if($venta->entregadoProductoApple)
        <p>
            <strong>Tipo:</strong> Producto Apple<br>
            {{ $venta->entregadoProductoApple->modelo }} · {{ $venta->entregadoProductoApple->capacidad }}<br>
            Color: {{ $venta->entregadoProductoApple->color }}<br>
            Batería: {{ $venta->entregadoProductoApple->bateria }}<br>
            Serie / IMEI: {{ $venta->entregadoProductoApple->numero_serie ?? $venta->entregadoProductoApple->imei_1 }}
        </p>
        <p>
            <strong>Valor aplicado:</strong>
            - Bs {{ number_format($venta->entregadoProductoApple->precio_costo, 2) }}
        </p>
        @endif
    </div>
    @endif

    <div class="divider"></div>

    <!-- TOTAL -->
    <div class="total">
        TOTAL Bs {{ number_format($totalAPagar, 2) }}
    </div>

    @if($venta->notas_adicionales)
    <div class="divider"></div>
    <div class="info">
        <strong>Notas:</strong><br>
        {{ $venta->notas_adicionales }}
    </div>
    @endif

    <!-- FIRMAS -->
    <div class="firmas">
        <div class="firma-box">
            <img src="{{ public_path('images/firma.png') }}">
            <div class="label">Firma autorizada<br>Apple Technology</div>
        </div>

        <div class="firma-box">
            <div style="height:45px;"></div>
            <div class="label">Firma del Cliente</div>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento sin valor fiscal<br>
        Garantía según condiciones del servicio
    </div>

</body>

</html>