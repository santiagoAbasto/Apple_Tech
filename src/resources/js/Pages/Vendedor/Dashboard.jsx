import VendedorLayout from '@/Layouts/VendedorLayout';
import { Head, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Dashboard({
  auth,
  resumen = {},
  ultimasVentas = [],
  ultimasCotizaciones = [],
  ultimosServicios = [],
}) {
  /* =======================
     Helpers
  ======================= */
  const fmt = (n) =>
    Number(n || 0).toLocaleString('es-BO', { minimumFractionDigits: 2 });

  const safeRoute = (name, params, fallback) => {
    try {
      return route(name, params, true);
    } catch {
      return fallback;
    }
  };

  const boletaHrefDeVenta = (v) => {
    if (v?.tipo_venta === 'servicio_tecnico') {
      const stId = v?.servicio_id ?? v?.id;
      return safeRoute(
        'vendedor.servicios.boleta',
        { servicio: stId },
        `/vendedor/servicios/${stId}/boleta`
      );
    }
    return safeRoute(
      'vendedor.ventas.boleta',
      { venta: v?.id },
      `/vendedor/ventas/${v?.id}/boleta`
    );
  };

  const boletaHrefDeServicio = (s) =>
    safeRoute(
      'vendedor.servicios.boleta',
      { servicio: s?.id },
      `/vendedor/servicios/${s?.id}/boleta`
    );

  const porcentajeMeta = Math.min(
    ((Number(resumen?.total_mes) || 0) /
      (Number(resumen?.meta_mensual) || 1)) *
      100,
    100
  ).toFixed(1);

  /* =======================
     RENDER
  ======================= */
  return (
    <VendedorLayout>
      <Head title="Dashboard Vendedor | AppleBoss" />

      {/* HEADER */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-green-700 mb-1">
          👨‍💼 Bienvenido, {auth?.user?.name}
        </h1>
        <p className="text-gray-600">
          Resumen de tu rendimiento y actividades recientes
        </p>
      </div>

      {/* ACCIONES RÁPIDAS */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <QuickAction
          href={route('vendedor.productos.index')}
          color="green"
          title="Ver Productos"
          desc="Inventario disponible"
          icon="fa-box-open"
        />

        <QuickAction
          href={route('vendedor.ventas.create')}
          color="sky"
          title="Registrar Venta"
          desc="Nueva venta"
          icon="fa-receipt"
        />

        <QuickAction
          href={route('vendedor.servicios.create')}
          color="yellow"
          title="Servicio Técnico"
          desc="Nuevo servicio"
          icon="fa-tools"
        />

        <QuickAction
          href={route('vendedor.cotizaciones.create')}
          color="indigo"
          title="Cotización"
          desc="Cotizar rápido"
          icon="fa-file-alt"
        />
      </div>

      {/* RESUMEN + META */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        <CardBox title="📊 Resumen del día">
          <Row label="Ventas del día" value={`Bs ${fmt(resumen?.ventas_dia)}`} />
          <Row label="Ganancia estimada" value={`Bs ${fmt(resumen?.ganancia_dia)}`} />
          <Row label="Cotizaciones" value={resumen?.cotizaciones_dia || 0} />
          <Row label="Servicios técnicos" value={resumen?.servicios_dia || 0} />
        </CardBox>

        <CardBox title="🎯 Meta Mensual">
          <div className="w-full bg-gray-200 h-5 rounded-full overflow-hidden">
            <div
              className="bg-green-600 h-full text-white text-sm font-bold flex items-center justify-end pr-2"
              style={{ width: `${porcentajeMeta}%` }}
            >
              {porcentajeMeta}%
            </div>
          </div>
          <p className="text-right text-sm text-gray-600 mt-2">
            Bs {fmt(resumen?.total_mes)} / Bs {fmt(resumen?.meta_mensual)}
          </p>
        </CardBox>
      </div>

      {/* ACTIVIDAD RECIENTE */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        {/* Ventas */}
        <ActivityBox title="🛒 Últimas Ventas" color="green">
          {ultimasVentas.length ? (
            ultimasVentas.map((v, i) => (
              <ActivityRow key={i}>
                <span>{v.nombre_cliente}</span>
                <div className="flex items-center gap-2">
                  <span>Bs {fmt(v.total)}</span>
                  <a
                    href={boletaHrefDeVenta(v)}
                    target="_blank"
                    rel="noopener"
                    className="btn btn-xs btn-outline-primary"
                  >
                    Boleta
                  </a>
                </div>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </ActivityBox>

        {/* Cotizaciones */}
        <ActivityBox title="📄 Cotizaciones" color="sky">
          {ultimasCotizaciones.length ? (
            ultimasCotizaciones.map((c, i) => (
              <ActivityRow key={i}>
                <span>{c.cliente}</span>
                <span>Bs {fmt(c.total)}</span>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </ActivityBox>

        {/* Servicios */}
        <ActivityBox title="🔧 Servicios Técnicos" color="yellow">
          {ultimosServicios.length ? (
            ultimosServicios.map((s, i) => (
              <ActivityRow key={i}>
                <span>{s.equipo}</span>
                <div className="flex items-center gap-2">
                  <span>Bs {fmt(s.precio_venta)}</span>
                  <a
                    href={boletaHrefDeServicio(s)}
                    target="_blank"
                    rel="noopener"
                    className="btn btn-xs btn-outline-primary"
                  >
                    Boleta
                  </a>
                </div>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </ActivityBox>
      </div>
    </VendedorLayout>
  );
}

/* =======================
   COMPONENTES AUX
======================= */

function QuickAction({ href, color, title, desc, icon }) {
  const border = {
    green: 'border-green-500 text-green-600',
    sky: 'border-sky-500 text-sky-600',
    yellow: 'border-yellow-500 text-yellow-600',
    indigo: 'border-indigo-500 text-indigo-600',
  };

  return (
    <Link
      href={href}
      className="transform hover:scale-105 transition bg-white p-6 rounded-xl shadow border-l-4 flex justify-between items-center"
    >
      <div>
        <p className={`text-sm font-bold uppercase ${border[color]}`}>{title}</p>
        <p className="text-lg font-semibold text-gray-800">{desc}</p>
      </div>
      <i className={`fas ${icon} fa-2x ${border[color]}`}></i>
    </Link>
  );
}

function CardBox({ title, children }) {
  return (
    <div className="bg-white rounded-xl shadow p-6">
      <h3 className="text-xl font-bold mb-4">{title}</h3>
      {children}
    </div>
  );
}

function Row({ label, value }) {
  return (
    <div className="flex justify-between py-2 border-b last:border-b-0">
      <span>{label}</span>
      <strong>{value}</strong>
    </div>
  );
}

function ActivityBox({ title, color, children }) {
  const bg = {
    green: 'bg-green-600',
    sky: 'bg-sky-600',
    yellow: 'bg-yellow-500 text-gray-900',
  };

  return (
    <div className="bg-white rounded-xl shadow">
      <div className={`${bg[color]} text-white p-4 rounded-t-xl`}>
        <h4 className="font-semibold">{title}</h4>
      </div>
      <ul className="divide-y p-4">{children}</ul>
    </div>
  );
}

function ActivityRow({ children }) {
  return (
    <li className="flex justify-between items-center py-2 text-sm text-gray-700">
      {children}
    </li>
  );
}

function Empty() {
  return (
    <li className="py-2 text-sm text-gray-500 text-center">
      Sin registros
    </li>
  );
}
