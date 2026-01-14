import AdminLayout from '@/Layouts/AdminLayout';
import { Head, Link } from '@inertiajs/react';
import { route } from 'ziggy-js';
import { useState } from 'react';
import axios from 'axios';

export default function Index({ ventas }) {
  const [codigoNota, setCodigoNota] = useState('');
  const [resultadosBusqueda, setResultadosBusqueda] = useState([]);

  const buscarNota = async (e) => {
    e.preventDefault();
    if (!codigoNota.trim()) return;

    try {
      const response = await axios.get(route('admin.ventas.buscarNota'), {
        params: { codigo_nota: codigoNota.trim() },
      });
      setResultadosBusqueda(response.data);
    } catch (error) {
      console.error('❌ Error al buscar nota:', error);
    }
  };

  /* ===============================
     DESGLOSE DE ITEMS
  =============================== */
  const itemsDesglosados = ventas.flatMap((venta) => {
    if (venta.tipo_venta === 'servicio_tecnico') {
      const precioVenta = parseFloat(venta.precio_venta || 0);
      const descuento = parseFloat(venta.descuento || 0);
      const capital = parseFloat(venta.precio_invertido || 0);
      const ganancia = precioVenta - descuento - capital;

      return [{
        cliente: venta.nombre_cliente,
        producto: 'Servicio Técnico',
        codigoNota: venta.servicio_tecnico?.codigo_nota ?? venta.codigo_nota,
        id_venta: venta.id,
        tipo: 'servicio_tecnico',
        precioVenta,
        descuento,
        permuta: 0,
        capital,
        precioFinal: precioVenta - descuento,
        ganancia,
        vendedor: venta.vendedor?.name || '—',
        fecha: venta.created_at,
      }];
    }

    return venta.items.map((item) => {
      const precioVenta = parseFloat(item.precio_venta || 0);
      const descuento = parseFloat(item.descuento || 0);
      const capital = parseFloat(item.precio_invertido || 0);
      const permuta = parseFloat(venta.valor_permuta || 0);
      const ganancia = precioVenta - descuento - permuta - capital;

      const nombre =
        item.tipo === 'celular'
          ? item.celular?.modelo
          : item.tipo === 'computadora'
          ? item.computadora?.nombre
          : item.tipo === 'producto_apple'
          ? item.producto_apple?.modelo
          : item.producto_general?.nombre;

      return {
        cliente: venta.nombre_cliente,
        producto: nombre,
        codigoNota: venta.codigo_nota,
        id_venta: venta.id,
        tipo: item.tipo,
        precioVenta,
        descuento,
        permuta,
        capital,
        precioFinal: precioVenta - descuento - permuta,
        ganancia,
        vendedor: venta.vendedor?.name || '—',
        fecha: venta.created_at,
      };
    });
  });

  const gananciaTotal = itemsDesglosados.reduce(
    (acc, i) => (i.ganancia > 0 ? acc + i.ganancia : acc),
    0
  );

  return (
    <AdminLayout>
      <Head title="Ventas Desglosadas" />

      {/* HEADER */}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <h1 className="text-2xl font-bold text-gray-800">
          📊 Ventas Desglosadas (Administrador)
        </h1>

        <Link
          href={route('admin.ventas.create')}
          className="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow transition"
        >
          ➕ Nueva Venta
        </Link>
      </div>

      {/* BUSCADOR */}
      <form
        onSubmit={buscarNota}
        className="flex flex-col sm:flex-row gap-3 mb-6"
      >
        <input
          value={codigoNota}
          onChange={(e) => setCodigoNota(e.target.value)}
          placeholder="Buscar por código o cliente"
          className="border border-gray-300 px-4 py-2 rounded-lg w-full sm:w-80 focus:ring-2 focus:ring-blue-500"
        />
        <button
          className="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition"
        >
          Buscar
        </button>
      </form>

      {/* RESULTADOS BUSQUEDA */}
      {resultadosBusqueda.length > 0 && (
        <div className="mb-8 bg-white rounded-xl shadow border">
          <div className="px-4 py-3 border-b bg-gray-50 font-semibold text-gray-700">
            Resultados encontrados
          </div>

          {resultadosBusqueda.map((r) => (
            <div
              key={r.id}
              className="flex flex-col sm:flex-row sm:items-center justify-between px-4 py-3 border-b last:border-b-0"
            >
              <div>
                <div className="font-mono text-blue-700 font-semibold">
                  {r.codigo_nota}
                </div>
                <div className="text-sm text-gray-600">
                  {r.nombre_cliente}
                </div>
              </div>

              <div className="flex gap-3 mt-2 sm:mt-0">
                <a
                  href={
                    r.tipo === 'servicio_tecnico'
                      ? route('admin.servicios.boleta', r.id_real)
                      : route('admin.ventas.boleta', r.id_real)
                  }
                  target="_blank"
                  className="text-sm font-medium text-blue-600 hover:underline"
                >
                  🧾 Normal
                </a>

                <a
                  href={
                    r.tipo === 'servicio_tecnico'
                      ? route('admin.servicios.recibo80mm', r.id_real)
                      : route('admin.ventas.boleta80', r.id_real)
                  }
                  target="_blank"
                  className="text-sm font-medium text-green-600 hover:underline"
                >
                  🖨 Térmica
                </a>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* TABLA */}
      <div className="bg-white rounded-xl shadow-xl overflow-x-auto border">
        <table className="w-full text-sm">
          <thead className="bg-blue-600 text-white text-xs uppercase">
            <tr>
              <th className="px-4 py-3 text-left">Cliente</th>
              <th className="px-4 py-3">Código</th>
              <th className="px-4 py-3">Producto</th>
              <th className="px-4 py-3 text-right">Venta</th>
              <th className="px-4 py-3 text-right">Desc.</th>
              <th className="px-4 py-3 text-right">Permuta</th>
              <th className="px-4 py-3 text-right">Capital</th>
              <th className="px-4 py-3 text-right">Final</th>
              <th className="px-4 py-3 text-right">Ganancia</th>
              <th className="px-4 py-3">Vendedor</th>
              <th className="px-4 py-3">Fecha</th>
              <th className="px-4 py-3 text-center">Boleta</th>
            </tr>
          </thead>

          <tbody>
            {itemsDesglosados.map((i, idx) => (
              <tr
                key={idx}
                className="border-t hover:bg-gray-50 transition"
              >
                <td className="px-4 py-3">{i.cliente}</td>
                <td className="px-4 py-3 font-mono text-blue-700">
                  {i.codigoNota}
                </td>
                <td className="px-4 py-3">{i.producto}</td>
                <td className="px-4 py-3 text-right">
                  {i.precioVenta.toFixed(2)}
                </td>
                <td className="px-4 py-3 text-right text-red-600">
                  -{i.descuento.toFixed(2)}
                </td>
                <td className="px-4 py-3 text-right text-yellow-600">
                  -{i.permuta.toFixed(2)}
                </td>
                <td className="px-4 py-3 text-right text-blue-600">
                  -{i.capital.toFixed(2)}
                </td>
                <td className="px-4 py-3 text-right font-medium">
                  {i.precioFinal.toFixed(2)}
                </td>
                <td
                  className={`px-4 py-3 text-right font-bold ${
                    i.ganancia < 0
                      ? 'text-red-600'
                      : 'text-green-600'
                  }`}
                >
                  {i.ganancia < 0
                    ? `Se invirtió ${Math.abs(i.ganancia).toFixed(2)}`
                    : i.ganancia.toFixed(2)}
                </td>
                <td className="px-4 py-3">{i.vendedor}</td>
                <td className="px-4 py-3 text-xs">
                  {new Date(i.fecha).toLocaleDateString('es-BO')}
                  <br />
                  <span className="text-gray-500">
                    {new Date(i.fecha).toLocaleTimeString('es-BO')}
                  </span>
                </td>
                <td className="px-4 py-3 text-center">
                  <div className="flex flex-col gap-1">
                    <a
                      href={route('admin.ventas.boleta', i.id_venta)}
                      target="_blank"
                      className="text-xs text-blue-600 hover:underline"
                    >
                      🧾 Normal
                    </a>
                    <a
                      href={route('admin.ventas.boleta80', i.id_venta)}
                      target="_blank"
                      className="text-xs text-green-600 hover:underline"
                    >
                      🖨 Térmica
                    </a>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        {/* RESUMEN */}
        <div className="px-6 py-4 border-t bg-gray-50 flex justify-end">
          <div className="text-right">
            <div className="text-sm text-gray-600">
              Ganancia Total Positiva
            </div>
            <div className="text-2xl font-bold text-green-600">
              {gananciaTotal.toFixed(2)} Bs
            </div>
          </div>
        </div>
      </div>
    </AdminLayout>
  );
}
