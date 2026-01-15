import VendedorLayout from '@/Layouts/VendedorLayout';
import { Head, router } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useState } from 'react';

import Ui3DCard from '@/components/Ui3DCard';
import AnimatedButton from '@/components/AnimatedButton';
import QuickActionCards from '@/components/QuickActionCards';
import UiversePanelCard from '@/components/UiversePanelCard';
import ConfirmLogoutModal from '@/components/ConfirmLogoutModal';

export default function Dashboard({
  auth,
  resumen = {},
  ultimasVentas = [],
  ultimasCotizaciones = [],
  ultimosServicios = [],
}) {
  /* =======================
     STATE LOGOUT MODAL
  ======================= */
  const [showLogout, setShowLogout] = useState(false);

  /* =======================
     COMPONENTES INTERNOS
  ======================= */
  function Row({ label, value }) {
    return (
      <div className="flex justify-between py-2 border-b border-gray-100 last:border-b-0 text-sm">
        <span className="text-gray-500">{label}</span>
        <strong className="text-gray-800">{value}</strong>
      </div>
    );
  }

  function ActivityRow({ children }) {
    return (
      <li className="flex justify-between items-center py-2 text-gray-700 text-sm">
        {children}
      </li>
    );
  }

  function Empty() {
    return (
      <li className="py-4 text-center text-gray-400 text-sm">
        Sin registros
      </li>
    );
  }

  /* =======================
     HELPERS
  ======================= */
  const fmt = (n) =>
    Number(n || 0).toLocaleString('es-BO', {
      minimumFractionDigits: 2,
    });

  const toNumberBO = (val) => {
    if (val === null || val === undefined) return 0;
    const s = String(val).trim();
    if (!s) return 0;

    if (s.includes(',')) {
      const cleaned = s.replace(/\./g, '').replace(',', '.');
      const n = parseFloat(cleaned);
      return Number.isFinite(n) ? n : 0;
    }

    const n = parseFloat(s);
    return Number.isFinite(n) ? n : 0;
  };

  const totalMes = toNumberBO(resumen?.total_mes);
  const metaMensual = toNumberBO(resumen?.meta_mensual);
  const porcentajeMeta = Math.min(
    (totalMes / (metaMensual || 1)) * 100,
    100
  );

  return (
    <VendedorLayout>
      <Head title="Dashboard Vendedor | AppleBoss" />

      {/* =======================
          HEADER
      ======================= */}
      <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-10">
        <div>
          <h1 className="text-2xl sm:text-3xl font-bold text-green-700">
            Bienvenido, {auth?.user?.name}
          </h1>
          <p className="text-sm text-gray-500">
            Resumen de tu rendimiento y actividades recientes
          </p>
        </div>

        {/* 🔴 BOTÓN LOGOUT */}
        <AnimatedButton onClick={() => setShowLogout(true)} />
      </div>

      {/* =======================
          ACCIONES RÁPIDAS
      ======================= */}
      <QuickActionCards
        actions={[
          { label: 'Productos', icon: 'fa-box-open', href: route('vendedor.productos.index') },
          { label: 'Venta', icon: 'fa-receipt', href: route('vendedor.ventas.create') },
          { label: 'Servicio', icon: 'fa-tools', href: route('vendedor.servicios.create') },
          { label: 'Cotizar', icon: 'fa-file-alt', href: route('vendedor.cotizaciones.create') },
        ]}
      />

      {/* =======================
          RESUMEN + META
      ======================= */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-14">
        <UiversePanelCard title="Resumen del día">
          <Row label="Ventas del día" value={`Bs ${fmt(resumen?.ventas_dia)}`} />
          <Row label="Ganancia estimada" value={`Bs ${fmt(resumen?.ganancia_dia)}`} />
          <Row label="Cotizaciones" value={resumen?.cotizaciones_dia || 0} />
          <Row label="Servicios técnicos" value={resumen?.servicios_dia || 0} />
        </UiversePanelCard>

        <Ui3DCard
          title="Meta mensual"
          description={`Bs ${fmt(totalMes)} / Bs ${fmt(metaMensual)}`}
          progress={Math.round(porcentajeMeta)}
        />
      </div>

      {/* =======================
          ACTIVIDAD RECIENTE
      ======================= */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <UiversePanelCard title="Últimas Ventas">
          {ultimasVentas.length ? (
            ultimasVentas.map((v, i) => (
              <ActivityRow key={i}>
                <span className="truncate">{v.nombre_cliente}</span>
                <span>Bs {fmt(v.total)}</span>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </UiversePanelCard>

        <UiversePanelCard title="Cotizaciones">
          {ultimasCotizaciones.length ? (
            ultimasCotizaciones.map((c, i) => (
              <ActivityRow key={i}>
                <span className="truncate">{c.cliente}</span>
                <span>Bs {fmt(c.total)}</span>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </UiversePanelCard>

        <UiversePanelCard title="Servicios Técnicos">
          {ultimosServicios.length ? (
            ultimosServicios.map((s, i) => (
              <ActivityRow key={i}>
                <span className="truncate">{s.equipo}</span>
                <span>Bs {fmt(s.precio_venta)}</span>
              </ActivityRow>
            ))
          ) : (
            <Empty />
          )}
        </UiversePanelCard>
      </div>

      {/* =======================
          MODAL LOGOUT PRO
      ======================= */}
      <ConfirmLogoutModal
        open={showLogout}
        onClose={() => setShowLogout(false)}
        onConfirm={() => router.post(route('logout'))}
      />
    </VendedorLayout>
  );
}
