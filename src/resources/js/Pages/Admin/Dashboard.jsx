import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import dayjs from 'dayjs';
import { route } from 'ziggy-js';

import QuickDateFilter from '@/Components/QuickDateFilter';
import EconomicCharts from '@/Components/EconomicCharts';
import SalesChart from '@/Components/SalesChart';

/* =======================
   HELPERS GLOBALES (ÚNICOS)
======================= */
const safeNum = (x) => {
  if (typeof x === 'number') return x;
  if (typeof x === 'string') {
    const s = x
      .replace(/Bs/gi, '')
      .replace(/\s+/g, '')
      .replace(/\./g, '')
      .replace(/,/g, '.');
    const n = Number(s);
    return isNaN(n) ? 0 : n;
  }
  return Number(x || 0) || 0;
};

const fmtBs = (n) =>
  `Bs ${safeNum(n).toLocaleString('es-BO', { minimumFractionDigits: 2 })}`;

/* 🔐 NORMALIZADOR DEFINITIVO PARA TOTALES */
const normalizeVentaTotal = (tipo, total) => {
  const t = String(tipo || '').toLowerCase();
  const n = safeNum(total);

  // servicio técnico viene en centavos
  if (t === 'servicio_tecnico') {
    return n / 100;
  }

  // protección extra por si viene inflado
  return n > 10000 ? n / 100 : n;
};

export default function Dashboard({
  user,
  resumen = {},
  resumen_total = {},
  vendedores = [],
  filtros = {},
  distribucion_economica = [],
}) {
  const hoyStr = dayjs().format('YYYY-MM-DD');

  const [fechaInicio, setFechaInicio] = useState(filtros.fecha_inicio || hoyStr);
  const [fechaFin, setFechaFin] = useState(filtros.fecha_fin || hoyStr);
  const [vendedorId, setVendedorId] = useState(filtros.vendedor_id || '');

  /* =======================
     UTILIDAD POST EGRESOS
  ======================= */
  const utilidadPostEgresos = (() => {
    if (Array.isArray(distribucion_economica)) {
      const u = distribucion_economica.find(d =>
        String(d?.label || '').toLowerCase().includes('utilidad')
      );
      if (u) return safeNum(u.valor);
    }

    if (resumen_total.utilidad_disponible !== undefined) {
      return safeNum(resumen_total.utilidad_disponible);
    }

    return (
      safeNum(resumen_total.ganancia_neta) -
      safeNum(resumen_total.egresos_total)
    );
  })();

  /* =======================
     FILTRO AUTOMÁTICO HOY
  ======================= */
  useEffect(() => {
    if (!filtros.fecha_inicio && !filtros.fecha_fin) {
      router.get(
        route('admin.dashboard'),
        { fecha_inicio: hoyStr, fecha_fin: hoyStr },
        { preserveState: true, preserveScroll: true }
      );
    }
  }, []);

  const handleFiltrar = (e) => {
    e.preventDefault();
    router.get(
      route('admin.dashboard'),
      {
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin,
        vendedor_id: vendedorId,
      },
      { preserveState: true, preserveScroll: true }
    );
  };

  const ultimasVentas = Array.isArray(resumen.ultimas_ventas)
    ? resumen.ultimas_ventas
    : [];

  return (
    <AdminLayout>
      <Head title="Panel de Administración | AppleBoss" />

      {/* ================= HEADER ================= */}
      <div className="px-4 mb-8">
        <div className="bg-gradient-to-r from-sky-600 to-sky-800 text-white rounded-xl p-6 shadow-lg">
          <h1 className="text-3xl font-bold">
            Bienvenido, {user?.name || 'Administrador'}
          </h1>
          <p className="text-sm opacity-90 mt-1">
            Resumen financiero y operativo del sistema
          </p>
        </div>
      </div>

      {/* ================= ACCIONES ================= */}
      <div className="flex flex-wrap gap-3 px-4 mb-10">
        <QuickButton routeName="admin.ventas.create" color="sky" text="➕ Venta" />
        <QuickButton routeName="admin.servicios.create" color="green" text="⚙️ Servicio" />
        <ProductoSelectorButton />
        <QuickButton routeName="admin.reportes.index" color="rose" text="📄 Reportes" />
      </div>

      {/* ================= TUS TARJETAS (INTOCABLES) ================= */}
      {/* BLOQUE 1 */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-4 mb-10">
        <Card titulo="Ventas Hoy" valor={fmtBs(resumen.ventas_hoy)} color="sky" />

        <Card
          titulo="Ganancia Neta (pre egresos)"
          valor={
            safeNum(resumen_total.ganancia_neta) < 0
              ? `Se invirtió ${fmtBs(Math.abs(safeNum(resumen_total.ganancia_neta)))}`
              : fmtBs(resumen_total.ganancia_neta)
          }
          color={safeNum(resumen_total.ganancia_neta) < 0 ? 'rose' : 'green'}
        />

        <Card titulo="Servicios Técnicos" valor={resumen.servicios || 0} color="indigo" />
        <Card titulo="Cotizaciones Enviadas" valor={resumen.cotizaciones || 0} color="rose" />
      </div>

      {/* BLOQUE 2 */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 px-4 mb-12">
        <Card titulo="Total Ventas (Precio final pagado)" valor={fmtBs(resumen_total.total_ventas)} color="sky" />
        <Card
          titulo="Inversión Total (Costo + Permuta)"
          valor={fmtBs(safeNum(resumen_total.total_costo) + safeNum(resumen_total.total_permuta))}
          color="indigo"
        />
        <Card titulo="Total Descuento" valor={fmtBs(resumen_total.total_descuento)} color="rose" />
        <Card
          titulo="Utilidad Disponible (ganancia - egresos)"
          valor={
            safeNum(resumen_total.utilidad_disponible) < 0
              ? `Se invirtió ${fmtBs(Math.abs(safeNum(resumen_total.utilidad_disponible)))}`
              : fmtBs(resumen_total.utilidad_disponible)
          }
          color={safeNum(resumen_total.utilidad_disponible) < 0 ? 'rose' : 'green'}
        />
      </div>

      {/* BLOQUE 3 */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 mb-10">
        <Card
          titulo="Productos Generales Disponibles"
          valor={resumen.stock_detalle?.productos_generales || 0}
          color="indigo"
        />
        <Card
          titulo="% del Stock Total"
          valor={`${resumen.stock_detalle?.porcentaje_productos_generales || 0}%`}
          color="sky"
        />
      </div>

      {/* ================= FILTROS ================= */}
      <QuickDateFilter vendedorId={vendedorId} />

      <form
        onSubmit={handleFiltrar}
        className="flex flex-wrap gap-4 items-end px-4 mb-12 bg-white rounded-xl shadow p-4"
      >
        <div>
          <label className="text-sm font-semibold">📅 Fecha inicio</label>
          <input
            type="date"
            className="form-input mt-1"
            value={fechaInicio}
            max={fechaFin}
            onChange={(e) => setFechaInicio(e.target.value)}
          />
        </div>

        <div>
          <label className="text-sm font-semibold">📅 Fecha fin</label>
          <input
            type="date"
            className="form-input mt-1"
            value={fechaFin}
            min={fechaInicio}
            max={hoyStr}
            onChange={(e) => setFechaFin(e.target.value)}
          />
        </div>

        <div>
          <label className="text-sm font-semibold">👤 Vendedor</label>
          <select
            className="form-select mt-1"
            value={vendedorId}
            onChange={(e) => setVendedorId(e.target.value)}
          >
            <option value="">Todos</option>
            {vendedores.map((v) => (
              <option key={v.id} value={v.id}>{v.name}</option>
            ))}
          </select>
        </div>

        <button className="btn bg-sky-700 text-white px-4 py-2 rounded-lg shadow">
          🔎 Filtrar
        </button>
      </form>

      {/* ================= GRÁFICOS (AMBOS) ================= */}
      <div className="px-4 mb-14">
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
          <div className="bg-white rounded-xl shadow p-6">
            <EconomicCharts
              resumen_total={resumen_total}
              distribucion_economica={distribucion_economica}
            />
          </div>

          <div className="bg-white rounded-xl shadow p-6">
            <SalesChart
              distribucion_economica={distribucion_economica}
              resumen_total={resumen_total}
            />
          </div>
        </div>
      </div>

      {/* ================= ÚLTIMAS 5 VENTAS (BUG CORREGIDO) ================= */}
      <div className="px-4 mb-12">
        <h2 className="text-lg font-semibold mb-3">🛒 Últimas 5 ventas</h2>
        <div className="overflow-auto bg-white rounded-xl shadow border">
          <table className="min-w-full text-sm">
            <thead className="bg-sky-100 text-sky-800">
              <tr>
                <th className="px-4 py-3">Producto</th>
                <th className="px-4 py-3">Tipo</th>
                <th className="px-4 py-3">Total</th>
                <th className="px-4 py-3">Fecha</th>
              </tr>
            </thead>
            <tbody>
              {ultimasVentas.map((v, i) => (
                <tr key={i} className="border-t hover:bg-gray-50">
                  <td className="px-4 py-2">{v.producto}</td>
                  <td className="px-4 py-2 capitalize">{v.tipo}</td>
                  <td className="px-4 py-2 font-semibold text-green-600">
                    {fmtBs(normalizeVentaTotal(v.tipo, v.total))}
                  </td>
                  <td className="px-4 py-2">
                    {dayjs(v.fecha).format('DD/MM/YYYY')}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </AdminLayout>
  );
}

/* =======================
   COMPONENTES AUX
======================= */

function Card({ titulo, valor, color }) {
  const colors = {
    sky: 'text-sky-700',
    green: 'text-green-600',
    indigo: 'text-indigo-600',
    rose: 'text-rose-600',
  };

  return (
    <div className="bg-white shadow-md rounded-xl p-5 text-center hover:shadow-lg transition">
      <p className="text-sm text-gray-500 mb-1">{titulo}</p>
      <h2 className={`text-xl font-bold ${colors[color]}`}>{valor}</h2>
    </div>
  );
}

function QuickButton({ routeName, color, text }) {
  const bg = {
    sky: 'bg-sky-600 hover:bg-sky-700',
    green: 'bg-green-600 hover:bg-green-700',
    rose: 'bg-rose-600 hover:bg-rose-700',
  };

  return (
    <Link
      href={route(routeName)}
      className={`${bg[color]} text-white rounded-lg px-4 py-3 font-semibold shadow`}
    >
      {text}
    </Link>
  );
}

function ProductoSelectorButton() {
  const [open, setOpen] = useState(false);

  const go = (tipo) => {
    const map = {
      celular: 'admin.celulares.create',
      computadora: 'admin.computadoras.create',
      producto_general: 'admin.productos-generales.create',
    };
    router.visit(route(map[tipo]));
  };

  return (
    <div className="relative">
      <button
        onClick={() => setOpen(!open)}
        className="bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-3 font-semibold shadow"
      >
        📦 Producto
      </button>

      {open && (
        <div className="absolute z-50 mt-2 bg-white rounded-xl shadow p-3 w-56">
          <button onClick={() => go('celular')} className="w-full text-left py-2 hover:bg-indigo-50">📱 Celular</button>
          <button onClick={() => go('computadora')} className="w-full text-left py-2 hover:bg-indigo-50">💻 Computadora</button>
          <button onClick={() => go('producto_general')} className="w-full text-left py-2 hover:bg-indigo-50">📦 Producto General</button>
        </div>
      )}
    </div>
  );
}
