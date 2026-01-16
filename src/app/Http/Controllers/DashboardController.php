<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\ServicioTecnico;
use App\Models\Celular;
use App\Models\Computadora;
use App\Models\ProductoGeneral;
use App\Models\ProductoApple;
use App\Models\Egreso;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $fechaInicio = request('fecha_inicio');
        $fechaFin    = request('fecha_fin');

        if (!$fechaInicio || !$fechaFin) {
            $fechaInicio = Venta::min('fecha') ?? now()->toDateString();
            $fechaFin    = now()->toDateString();
        }

        $vendedorId  = request('vendedor_id');

        /* =========================
         * VENTAS (SIN TOCAR)
         * ========================= */
        $ventas = Venta::with([
            'vendedor',
            'items.celular',
            'items.computadora',
            'items.productoGeneral',
            'items.productoApple',
            'entregadoCelular',
            'entregadoComputadora',
            'entregadoProductoGeneral',
            'entregadoProductoApple',
        ])
            ->when($vendedorId, fn($q) => $q->where('user_id', $vendedorId))
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get();

        /* =========================
         * SERVICIOS TÉCNICOS REALES
         * ========================= */
        $serviciosTecnicos = ServicioTecnico::with('vendedor')
            ->when($vendedorId, fn($q) => $q->where('user_id', $vendedorId))
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get();

        $items = collect();
        $inversionTotal = 0;

        $ganancias = [
            'celulares' => 0,
            'computadoras' => 0,
            'generales' => 0,
            'servicio_tecnico' => 0,
            'producto_apple' => 0,
        ];

        /* =========================
         * PROCESAR VENTAS (IGUAL QUE ANTES)
         * ========================= */
        foreach ($ventas as $venta) {
            $permutaCosto = optional($venta->entregadoCelular)->precio_costo
                ?? optional($venta->entregadoComputadora)->precio_costo
                ?? optional($venta->entregadoProductoGeneral)->precio_costo
                ?? optional($venta->entregadoProductoApple)->precio_costo
                ?? 0;

            $permutaAplicada = false;

            foreach ($venta->items as $item) {
                $aplicaPermuta = in_array($item->tipo, ['celular', 'computadora', 'producto_apple']) && !$permutaAplicada;
                $permuta = $aplicaPermuta ? $permutaCosto : 0;
                $permutaAplicada = $aplicaPermuta;

                $ganancia = $item->precio_venta - $item->descuento - $permuta - $item->precio_invertido;
                $inversionTotal += $item->precio_invertido;

                match ($item->tipo) {
                    'celular'          => $ganancias['celulares']       += $ganancia,
                    'computadora'      => $ganancias['computadoras']    += $ganancia,
                    'producto_general' => $ganancias['generales']       += $ganancia,
                    'producto_apple'   => $ganancias['producto_apple']  += $ganancia,
                    default            => null,
                };

                $productoNombre = match ($item->tipo) {
                    'celular'          => $item->celular?->modelo ?? 'Celular',
                    'computadora'      => $item->computadora?->nombre ?? 'Computadora',
                    'producto_general' => $item->productoGeneral?->nombre ?? 'Producto General',
                    'producto_apple'   => $item->productoApple?->modelo ?? 'Producto Apple',
                    default            => '—',
                };

                $items->push([
                    'fecha'     => $venta->fecha,
                    'producto'  => $productoNombre,
                    'tipo'      => $item->tipo,
                    'ganancia'  => $ganancia,
                    'descuento' => $item->descuento,
                    'permuta'   => $permuta,
                    'capital'   => $item->precio_invertido,
                    'subtotal'  => $item->precio_venta - $item->descuento - $permuta,
                    'vendedor'  => $venta->vendedor?->name ?? '—',
                ]);
            }

            // ⛔ LÓGICA ANTIGUA SE MANTIENE (DATOS HISTÓRICOS)
            if ($venta->tipo_venta === 'servicio_tecnico' && $venta->items->isEmpty()) {
                $ganancia = $venta->precio_venta - $venta->descuento - $venta->precio_invertido;
                $ganancias['servicio_tecnico'] += $ganancia;
                $inversionTotal += $venta->precio_invertido;

                $items->push([
                    'fecha'     => $venta->fecha,
                    'producto'  => 'Servicio Técnico',
                    'tipo'      => 'servicio_tecnico',
                    'ganancia'  => $ganancia,
                    'descuento' => $venta->descuento,
                    'permuta'   => 0,
                    'capital'   => $venta->precio_invertido,
                    'subtotal'  => $venta->precio_venta - $venta->descuento,
                    'vendedor'  => $venta->vendedor?->name ?? '—',
                ]);
            }
        }

        /* =========================
         * 🔧 NUEVO: SERVICIOS TÉCNICOS REALES
         * ========================= */
        foreach ($serviciosTecnicos as $servicio) {
            $ganancia = $servicio->precio_venta - $servicio->precio_costo;
            $ganancias['servicio_tecnico'] += $ganancia;
            $inversionTotal += $servicio->precio_costo;

            $items->push([
                'fecha'     => $servicio->fecha,
                'producto'  => 'Servicio Técnico',
                'tipo'      => 'servicio_tecnico',
                'ganancia'  => $ganancia,
                'descuento' => 0,
                'permuta'   => 0,
                'capital'   => $servicio->precio_costo,
                'subtotal'  => $servicio->precio_venta,
                'vendedor'  => $servicio->vendedor?->name ?? '—',
            ]);
        }

        // =========================
        // 🔻 EGRESOS (día/mes/año)  ✅ ANTES DEL HISTÓRICO
        // =========================
        $egresosCollection = Egreso::whereBetween('created_at', [
            $fechaInicio . ' 00:00:00',
            $fechaFin    . ' 23:59:59',
        ])->get(['created_at', 'precio_invertido']);

        // Por día (YYYY-MM-DD)
        $egresosPorDia = $egresosCollection
            ->groupBy(fn($e) => $e->created_at->toDateString())
            ->map(fn($grp) => $grp->sum('precio_invertido'));

        // Por mes (YYYY-MM)
        $egresosPorMes = $egresosCollection
            ->groupBy(fn($e) => $e->created_at->format('Y-m'))
            ->map(fn($grp) => $grp->sum('precio_invertido'));

        // Por año (YYYY)
        $egresosPorAnio = $egresosCollection
            ->groupBy(fn($e) => $e->created_at->format('Y'))
            ->map(fn($grp) => $grp->sum('precio_invertido'));

        $totalEgresos = $egresosCollection->sum('precio_invertido');

        /* =====================================================
         * 📈 HISTÓRICO PARA SVG (DÍA / MES / AÑO) ✅ POST-EGRESOS
         * - CLAVE: utilidad = ganancia - egresos del periodo
         * ===================================================== */

        // 📅 DÍA → agrupado por día (para que el chart tenga "base" real por día)
        $historicoDia = $items
            ->sortBy('fecha')
            ->values()
            ->map(function ($i) use ($egresosPorDia) {
                $fecha = Carbon::parse($i['fecha'])->toDateString();
                $egresoDia = $egresosPorDia[$fecha] ?? 0;

                return [
                    'fecha'    => Carbon::parse($i['fecha'])->toDateTimeString(),
                    'total'    => $i['subtotal'],
                    'capital'  => $i['capital'],
                    // 🔴 CLAVE: utilidad real por movimiento
                    'utilidad' => $i['ganancia'] - $egresoDia,
                ];
            });

        // 📆 MES → por DÍA (utilidad diaria post-egresos)
        $historicoMes = $items
            ->groupBy(fn($i) => Carbon::parse($i['fecha'])->toDateString())
            ->map(function ($grp, $fecha) use ($egresosPorDia) {
                $gananciaDia = $grp->sum('ganancia');
                $egresoDia   = $egresosPorDia[$fecha] ?? 0;

                return [
                    'fecha'    => $fecha,
                    'total'    => $grp->sum('subtotal'),
                    'capital'  => $grp->sum('capital'),
                    'utilidad' => $gananciaDia - $egresoDia, // 🔴 post-egresos por día
                ];
            })
            ->values();

        // 📈 AÑO → por MES (utilidad mensual post-egresos)
        $historicoAnio = $items
            ->groupBy(fn($i) => Carbon::parse($i['fecha'])->format('Y-m'))
            ->map(function ($grp, $ym) use ($egresosPorMes) {
                $gananciaMes = $grp->sum('ganancia');
                $egresoMes   = $egresosPorMes[$ym] ?? 0;

                return [
                    'fecha'    => $ym . '-01',
                    'total'    => $grp->sum('subtotal'),
                    'capital'  => $grp->sum('capital'),
                    'utilidad' => $gananciaMes - $egresoMes, // 🔴 post-egresos por mes
                ];
            })
            ->values();

        $gananciaNeta       = $items->sum('ganancia');
        $utilidadDisponible = $gananciaNeta - $totalEgresos;

        // ✅ Ventas hoy: calcula desde $items, no desde $ventas
        $ventasHoy = $items->where('fecha', today()->toDateString())->sum('subtotal');

        // Stocks
        $stockCel   = Celular::where('estado', 'disponible')->count();
        $stockComp  = Computadora::where('estado', 'disponible')->count();
        $stockGen   = ProductoGeneral::where('estado', 'disponible')->count();
        $stockApple = ProductoApple::where('estado', 'disponible')->count();
        $stockTotal = $stockCel + $stockComp + $stockGen + $stockApple;

        // Últimas 5 ventas (desde items)
        $ultimasVentas = $items->sortByDesc('fecha')->take(5)->values()->map(fn($i) => [
            'cliente' => $i['vendedor'],
            'producto' => $i['producto'],
            'tipo'    => $i['tipo'],
            'total'   => $i['subtotal'],
            'fecha'   => $i['fecha'],
        ]);

        // ✅ Resumen por día con egresos y utilidad disponible
        $resumenGrafico = $items->groupBy('fecha')->map(function ($itemsDelDia, $fecha) use ($egresosPorDia) {
            $egresosDia   = $egresosPorDia[$fecha] ?? 0;
            $gananciaDia  = $itemsDelDia->sum('ganancia');

            return [
                'fecha'                        => $fecha,
                'total'                        => $itemsDelDia->sum(fn($i) => $i['ganancia'] + $i['capital'] + $i['descuento'] + $i['permuta']),
                'ganancia_productos'           => $itemsDelDia->whereIn('tipo', ['celular', 'computadora', 'producto_apple'])->sum('ganancia'),
                'ganancia_productos_generales' => $itemsDelDia->where('tipo', 'producto_general')->sum('ganancia'),
                'ganancia_servicios'           => $itemsDelDia->where('tipo', 'servicio_tecnico')->sum('ganancia'),
                'descuento'                    => $itemsDelDia->sum('descuento'),
                // 🔻 nuevos
                'egresos'                      => $egresosDia,
                'utilidad_disponible'          => $gananciaDia - $egresosDia,
            ];
        })->values();

        // =========================
        // 🔸 Distribución económica
        // =========================
        $distribucionEconomica = [
            [
                'label' => 'Inversión',
                'valor' => $items->sum('capital'),
            ],
            [
                'label' => 'Descuento',
                'valor' => $items->sum('descuento'),
            ],
            [
                'label' => 'Permuta',
                'valor' => $items->sum('permuta'),
            ],
            [
                'label' => 'Utilidad (post egresos)',
                'valor' => max($utilidadDisponible, 0),
            ],
            // [
            //     'label' => 'Egresos',
            //     'valor' => $totalEgresos,
            // ],
        ];

        return Inertia::render('Admin/Dashboard', [
            'user' => Auth::user(),

            'resumen' => [
                'ventas_hoy' => $ventasHoy,
                'stock_total' => $stockTotal,
                'stock_detalle' => [
                    'celulares' => $stockCel,
                    'computadoras' => $stockComp,
                    'productos_generales' => $stockGen,
                    'productos_apple' => $stockApple,
                    'porcentaje_productos_generales' => $stockTotal > 0 ? round(($stockGen / $stockTotal) * 100, 1) : 0,
                ],
                'permutas' => $ventas->where('es_permuta', true)->count(),
                'servicios' =>
                $ventas->where('tipo_venta', 'servicio_tecnico')->count()
                    + $serviciosTecnicos->count(),
                'ganancia_neta' => $gananciaNeta,
                'egresos' => $totalEgresos,
                'utilidad_disponible' => $utilidadDisponible,
                'ventas_con_descuento' => $items->where('descuento', '>', 0)->count(),
                'ultimas_ventas' => $ultimasVentas,
                'cotizaciones' => 0,
            ],

            'resumen_total' => [
                'total_ventas' => $items->sum('subtotal'),
                'total_descuento' => $items->sum('descuento'),
                'total_costo' => $items->sum('capital'),
                'total_permuta' => $items->sum('permuta'),
                'ganancia_neta' => $gananciaNeta,
                'egresos_total' => $totalEgresos,
                'utilidad_disponible' => $utilidadDisponible,
                'ganancia_productos' => $ganancias['celulares'] + $ganancias['computadoras'] + $ganancias['producto_apple'],
                'ganancia_productos_generales' => $ganancias['generales'],
                'ganancia_servicios' => $ganancias['servicio_tecnico'],
            ],

            'distribucion_economica' => $distribucionEconomica,

            'resumen_grafico' => $resumenGrafico,
            'historico' => [
                'dia'  => $historicoDia,
                'mes'  => $historicoMes,
                'anio' => $historicoAnio,
            ],

            // 🔹 Egresos agrupados para usar en tabs/filtros del front (día/mes/año)
            'egresos_agrupados' => [
                'por_dia'  => $egresosPorDia,
                'por_mes'  => $egresosPorMes,
                'por_anio' => $egresosPorAnio,
            ],

            'vendedores' => User::where('rol', 'vendedor')->select('id', 'name')->get(),

            'filtros' => [
                'fecha_inicio' => $fechaInicio,
                'fecha_fin'    => $fechaFin,
                'vendedor_id'  => $vendedorId,
            ],
        ]);
    }
}
