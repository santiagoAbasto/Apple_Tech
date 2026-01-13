import VendedorLayout from '@/Layouts/VendedorLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import dayjs from 'dayjs';
import { route } from 'ziggy-js';
import axios from 'axios';

/* ======================
   UTILIDAD VISUAL
====================== */
const formatServiciosInline = (detalle) => {
  try {
    const items = JSON.parse(detalle);
    if (!Array.isArray(items)) return '';
    return items
      .map(i => `${i.descripcion} Bs ${Number(i.precio).toLocaleString('es-BO')}`)
      .join(' • ');
  } catch {
    return '';
  }
};

export default function Index({ servicios = [], filtros = {} }) {
  const [fechaInicio, setFechaInicio] = useState(filtros.fecha_inicio || '');
  const [fechaFin, setFechaFin] = useState(filtros.fecha_fin || '');
  const [buscar, setBuscar] = useState('');
  const [resultadosBusqueda, setResultadosBusqueda] = useState([]);

  const handleFiltrar = (e) => {
    e.preventDefault();
    router.get(route('vendedor.servicios.index'), {
      fecha_inicio: fechaInicio,
      fecha_fin: fechaFin,
    });
  };

  const handleExportar = () => {
    const params = new URLSearchParams({
      fecha_inicio: fechaInicio,
      fecha_fin: fechaFin,
    });
    window.open(
      route('vendedor.servicios.exportarFiltrado') + `?${params.toString()}`,
      '_blank'
    );
  };

  const handleExportarResumen = () => {
    const params = new URLSearchParams({
      fecha_inicio: fechaInicio,
      fecha_fin: fechaFin,
    });
    window.open(
      route('vendedor.servicios.exportarResumen') + `?${params.toString()}`,
      '_blank'
    );
  };

  const buscarServicio = async (e) => {
    e.preventDefault();
    if (!buscar.trim()) return;

    const res = await axios.get(route('vendedor.servicios.buscar'), {
      params: { buscar: buscar.trim() },
    });

    setResultadosBusqueda(res.data.servicios || []);
  };

  const serviciosMostrados =
    resultadosBusqueda.length > 0 ? resultadosBusqueda : servicios;

  return (
    <VendedorLayout>
      <Head title="Mis Servicios Técnicos" />

      {/* HEADER */}
      <div className="mb-6">
        <h1 className="text-2xl font-semibold text-slate-800">
          Servicios Técnicos
        </h1>
        <p className="text-sm text-slate-500">
          Registro y control de trabajos técnicos realizados
        </p>
      </div>

      {/* BUSCADOR */}
      <form onSubmit={buscarServicio} className="flex gap-3 mb-6">
        <input
          type="text"
          value={buscar}
          onChange={(e) => setBuscar(e.target.value)}
          placeholder="Buscar por cliente o código"
          className="w-72 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500"
        />
        <button className="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm">
          Buscar
        </button>
      </form>

      {/* FILTROS */}
      <form
        onSubmit={handleFiltrar}
        className="grid md:grid-cols-4 gap-4 items-end bg-white p-4 rounded-xl shadow mb-6"
      >
        <div>
          <label className="text-xs text-slate-500">Desde</label>
          <input
            type="date"
            className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            value={fechaInicio}
            onChange={(e) => setFechaInicio(e.target.value)}
          />
        </div>

        <div>
          <label className="text-xs text-slate-500">Hasta</label>
          <input
            type="date"
            className="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
            value={fechaFin}
            onChange={(e) => setFechaFin(e.target.value)}
          />
        </div>

        <button className="h-10 rounded-lg bg-slate-800 text-white text-sm">
          Filtrar
        </button>

        <div className="flex gap-2">
          <button
            type="button"
            onClick={handleExportar}
            className="w-full h-10 rounded-lg border border-blue-600 text-blue-600 text-sm"
          >
            Boletas
          </button>
          <button
            type="button"
            onClick={handleExportarResumen}
            className="w-full h-10 rounded-lg border border-green-600 text-green-600 text-sm"
          >
            Resumen
          </button>
        </div>
      </form>

      {/* TABLA */}
      <div className="overflow-x-auto bg-white rounded-xl shadow border">
        <table className="min-w-full text-sm">
          <thead className="bg-slate-100 text-slate-700 uppercase text-xs">
            <tr>
              <th className="px-4 py-3 text-center">#</th>
              <th className="px-4 py-3 text-left">Cliente</th>
              <th className="px-4 py-3 text-left">Código</th>
              <th className="px-4 py-3 text-left">Equipo</th>
              <th className="px-4 py-3 text-left">Servicios</th>
              <th className="px-4 py-3 text-center">Fecha</th>
              <th className="px-4 py-3 text-right">Total</th>
              <th className="px-4 py-3 text-center">Boleta</th>
            </tr>
          </thead>

          <tbody className="divide-y">
            {serviciosMostrados.length === 0 ? (
              <tr>
                <td colSpan="8" className="text-center py-6 text-slate-400">
                  No hay servicios registrados
                </td>
              </tr>
            ) : (
              serviciosMostrados.map((s, i) => (
                <tr key={s.id} className="hover:bg-slate-50">
                  <td className="px-4 py-2 text-center">{i + 1}</td>
                  <td className="px-4 py-2">{s.cliente}</td>
                  <td className="px-4 py-2 font-mono text-blue-600">
                    {s.codigo_nota}
                  </td>
                  <td className="px-4 py-2">{s.equipo}</td>

                  {/* 🔥 SERVICIOS LIMPIOS */}
                  <td className="px-4 py-2 max-w-[420px]">
                    <div
                      className="truncate text-slate-700"
                      title={formatServiciosInline(s.detalle_servicio)}
                    >
                      {formatServiciosInline(s.detalle_servicio)}
                    </div>
                  </td>

                  <td className="px-4 py-2 text-center">
                    {dayjs(s.fecha).format('DD/MM/YYYY')}
                  </td>

                  <td className="px-4 py-2 text-right font-semibold text-green-600">
                    Bs {Number(s.precio_venta).toLocaleString('es-BO')}
                  </td>

                  <td className="px-4 py-2 text-center">
                    <a
                      href={route('vendedor.servicios.boleta', { servicio: s.id })}
                      target="_blank"
                      className="text-blue-600 hover:underline"
                    >
                      Ver
                    </a>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </VendedorLayout>
  );
}
