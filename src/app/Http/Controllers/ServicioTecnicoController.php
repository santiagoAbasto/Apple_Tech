<?php

namespace App\Http\Controllers;

use App\Models\ServicioTecnico;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Services\GeneradorCodigos;

class ServicioTecnicoController extends Controller
{
    /* ======================================================
     * INDEX
     * ====================================================== */
    public function index(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'vendedor_id' => 'nullable|exists:users,id',
            'buscar' => 'nullable|string',
        ]);

        $query = ServicioTecnico::with('vendedor')->orderByDesc('fecha');

        if (Auth::user()->rol === 'vendedor') {
            $query->where('user_id', Auth::id());
        } elseif ($request->filled('vendedor_id')) {
            $query->where('user_id', $request->vendedor_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('cliente', 'like', '%' . $request->buscar . '%')
                  ->orWhere('codigo_nota', 'like', '%' . $request->buscar . '%');
            });

            return response()->json([
                'servicios' => $query->get(),
            ]);
        }

        return Inertia::render(
            Auth::user()->rol === 'admin'
                ? 'Admin/Servicios/Index'
                : 'Vendedor/Servicios/Index',
            [
                'servicios' => $query->get(),
                'filtros' => $request->only(['fecha_inicio', 'fecha_fin', 'vendedor_id']),
                'vendedores' => Auth::user()->rol === 'admin'
                    ? \App\Models\User::where('rol', 'vendedor')->select('id', 'name')->get()
                    : [],
            ]
        );
    }

    /* ======================================================
     * CREATE
     * ====================================================== */
    public function create()
    {
        return Inertia::render(
            Auth::user()->rol === 'admin'
                ? 'Admin/Servicios/Create'
                : 'Vendedor/Servicios/Create'
        );
    }

    /* ======================================================
     * STORE (CLAVE – CON GENERADOR CORRELATIVO)
     * ====================================================== */
public function store(Request $request)
{
    $data = $request->validate([
        'cliente'          => 'required|string',
        'telefono'         => 'nullable|string',
        'equipo'           => 'required|string',
        'detalle_servicio' => 'required|string',
        'precio_costo'     => 'required|numeric|min:0',
        'precio_venta'     => 'required|numeric|min:0',
        'tecnico'          => 'required|string',
        'fecha'            => 'nullable|date',
    ]);

    return DB::transaction(function () use ($data) {

        $fecha = $data['fecha'] ?? now('America/La_Paz');

        // 🔐 Código correlativo REAL AT-ST###
        $codigoServicio = GeneradorCodigos::siguienteServicioTecnico();

        ServicioTecnico::create([
            'codigo_nota'      => $codigoServicio,
            'cliente'          => $data['cliente'],
            'telefono'         => $data['telefono'],
            'equipo'           => $data['equipo'],
            'detalle_servicio' => $data['detalle_servicio'],
            'precio_costo'     => $data['precio_costo'],
            'precio_venta'     => $data['precio_venta'],
            'tecnico'          => $data['tecnico'],
            'fecha'            => $fecha,
            'user_id'          => auth()->id(),
        ]);

        return redirect()
            ->route(auth()->user()->rol === 'admin'
                ? 'admin.servicios.index'
                : 'vendedor.servicios.index'
            )
            ->with('success', 'Servicio técnico registrado correctamente.');
    });
}


    /* ======================================================
     * EXPORTACIONES
     * ====================================================== */
    public function exportarFiltrado(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'vendedor_id' => 'nullable|exists:users,id',
        ]);

        $query = ServicioTecnico::with('vendedor')->orderByDesc('fecha');

        if (Auth::user()->rol === 'vendedor') {
            $query->where('user_id', Auth::id());
        } elseif ($request->filled('vendedor_id')) {
            $query->where('user_id', $request->vendedor_id);
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);
        }

        $servicios = $query->get();

        return Pdf::loadView('pdf.servicios_tecnicos_resumen', compact('servicios'))
            ->setPaper('A4', 'landscape')
            ->download('servicios_tecnicos_filtrado.pdf');
    }

    public function exportar(Request $request)
    {
        return $this->generarPDF(
            ServicioTecnico::with('vendedor')->orderByDesc('fecha')->get()
        );
    }

    public function exportarDia()
    {
        return $this->generarPDF(
            ServicioTecnico::whereDate('fecha', Carbon::now('America/La_Paz'))->get()
        );
    }

    public function exportarSemana()
    {
        return $this->generarPDF(
            ServicioTecnico::whereBetween('fecha', [
                Carbon::now('America/La_Paz')->startOfWeek(),
                Carbon::now('America/La_Paz')->endOfWeek()
            ])->get()
        );
    }

    public function exportarMes()
    {
        return $this->generarPDF(
            ServicioTecnico::whereBetween('fecha', [
                Carbon::now('America/La_Paz')->startOfMonth(),
                Carbon::now('America/La_Paz')->endOfMonth()
            ])->get()
        );
    }

    public function exportarAnio()
    {
        return $this->generarPDF(
            ServicioTecnico::whereBetween('fecha', [
                Carbon::now('America/La_Paz')->startOfYear(),
                Carbon::now('America/La_Paz')->endOfYear()
            ])->get()
        );
    }

    private function generarPDF($servicios)
    {
        return Pdf::loadView('pdf.servicios_tecnicos', compact('servicios'))
            ->setPaper('A4', 'portrait')
            ->download('reporte_servicios.pdf');
    }

    /* ======================================================
     * BOLETA
     * ====================================================== */
    public function boleta(ServicioTecnico $servicio)
    {
        $servicio->load('vendedor');

        return Pdf::loadView('pdf.boleta_servicio', compact('servicio'))
            ->stream("boleta-servicio-{$servicio->codigo_nota}.pdf");
    }

    /* ======================================================
     * BUSCAR
     * ====================================================== */
    public function buscar(Request $request)
    {
        $request->validate(['buscar' => 'required|string']);

        return response()->json([
            'servicios' => ServicioTecnico::where('user_id', Auth::id())
                ->where(function ($q) use ($request) {
                    $q->where('cliente', 'like', '%' . $request->buscar . '%')
                      ->orWhere('codigo_nota', 'like', '%' . $request->buscar . '%');
                })
                ->orderByDesc('fecha')
                ->take(10)
                ->get()
        ]);
    }

    public function exportarResumen(Request $request)
    {
        $servicios = ServicioTecnico::with('vendedor')
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin])
            ->orderByDesc('fecha')
            ->get();

        return Pdf::loadView('pdf.servicios_tecnicos_resumen', compact('servicios'))
            ->setPaper('A4', 'landscape')
            ->stream('servicios_tecnicos_resumen.pdf');
    }
}
