<?php

namespace App\Http\Controllers\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Cotizacion;
use App\Models\ServicioTecnico;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardVendedorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hoy = now()->toDateString();
        $mesActual = now()->month;

        /* =====================================================
         |  VENTAS DEL DÍA (con permuta y múltiples ítems)
         ===================================================== */
        $ventasHoy = Venta::with([
            'items',
            'entregadoCelular',
            'entregadoComputadora',
            'entregadoProductoGeneral',
            'entregadoProductoApple'
        ])
            ->where('user_id', $user->id)
            ->whereDate('fecha', $hoy)
            ->get();

        $gananciaHoy = 0;

        foreach ($ventasHoy as $venta) {
            $permuta = optional($venta->entregadoCelular)->precio_costo
                ?? optional($venta->entregadoComputadora)->precio_costo
                ?? optional($venta->entregadoProductoGeneral)->precio_costo
                ?? optional($venta->entregadoProductoApple)->precio_costo
                ?? 0;

            // Ventas con ítems
            foreach ($venta->items as $item) {
                $aplicaPermuta = in_array($item->tipo, ['celular', 'computadora', 'producto_apple']);
                $permutaReal = $aplicaPermuta ? $permuta : 0;

                $gananciaHoy +=
                    $item->precio_venta
                    - $item->descuento
                    - $permutaReal
                    - $item->precio_invertido;
            }

            // Servicio técnico sin ítems
            if ($venta->tipo_venta === 'servicio_tecnico' && $venta->items->isEmpty()) {
                $gananciaHoy +=
                    $venta->precio_venta
                    - $venta->descuento
                    - $venta->precio_invertido;
            }
        }

        /* =====================================================
         |  ÚLTIMOS REGISTROS
         ===================================================== */
        $ultimasVentas = Venta::with(['items', 'servicioTecnico'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $ultimasCotizaciones = Cotizacion::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $ultimosServicios = ServicioTecnico::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        /* =====================================================
         |  MÉTRICAS DEL DÍA
         ===================================================== */
        $cotizacionesDia = $ultimasCotizaciones
            ->where('created_at', '>=', $hoy)
            ->count();

        $serviciosDia = $ultimosServicios
            ->where('fecha', $hoy)
            ->count();

        /* =====================================================
         |  TOTAL DEL MES (VENTAS + SERVICIOS)
         ===================================================== */

        // Ventas del mes
        $totalVentasMes = Venta::with('items')
            ->where('user_id', $user->id)
            ->whereMonth('fecha', $mesActual)
            ->get()
            ->sum(function ($v) {
                if ($v->tipo_venta === 'servicio_tecnico' && $v->items->isEmpty()) {
                    return $v->precio_venta - $v->descuento;
                }

                return $v->items->sum(fn($i) => $i->precio_venta - $i->descuento);
            });

        // Servicios técnicos del mes (tabla propia)
        $totalServiciosMes = ServicioTecnico::where('user_id', $user->id)
            ->whereMonth('fecha', $mesActual)
            ->sum('precio_venta');

        $totalMes = $totalVentasMes + $totalServiciosMes;

        /* =====================================================
         |  RENDER INERTIA
         ===================================================== */
        return Inertia::render('Vendedor/Dashboard', [
            'auth' => [
                'user' => $user
            ],

            'resumen' => [
                'ventas_dia' => $ventasHoy->sum(function ($v) {
                    if ($v->tipo_venta === 'servicio_tecnico' && $v->items->isEmpty()) {
                        return $v->precio_venta - $v->descuento;
                    }

                    return $v->items->sum(fn($i) => $i->precio_venta - $i->descuento);
                }),

                'ganancia_dia'      => $gananciaHoy,
                'cotizaciones_dia'  => $cotizacionesDia,
                'servicios_dia'     => $serviciosDia,
                'total_mes'         => $totalMes,
                'meta_mensual'      => 10000,
            ],

            'ultimasVentas' => $ultimasVentas->map(function ($v) {
                $monto = $v->tipo_venta === 'servicio_tecnico' && $v->items->isEmpty()
                    ? $v->precio_venta - $v->descuento
                    : $v->items->sum(fn($i) => $i->precio_venta - $i->descuento);

                return [
                    'id'             => $v->id,
                    'tipo_venta'     => $v->tipo_venta,
                    'servicio_id'    => optional($v->servicioTecnico)->id,
                    'nombre_cliente' => $v->nombre_cliente,
                    'total'          => $monto,
                    'fecha'          => $v->fecha,
                ];
            }),

            'ultimasCotizaciones' => $ultimasCotizaciones->map(fn($c) => [
                'id'             => $c->id,
                'nombre_cliente' => $c->nombre_cliente,
                'total'          => $c->total,
                'fecha'          => $c->created_at->toDateString(),
            ]),

            'ultimosServicios' => $ultimosServicios->map(fn($s) => [
                'id'           => $s->id,
                'equipo'       => $s->equipo,
                'precio_venta' => $s->precio_venta,
                'fecha'        => $s->fecha,
            ]),
        ]);
    }
}
