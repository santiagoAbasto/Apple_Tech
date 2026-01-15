import React, { useState } from 'react';
import { Link, Head, router } from '@inertiajs/react';
import VendedorLayout from '@/Layouts/VendedorLayout';
import FancyButton from '@/Components/FancyButton';
import { route } from 'ziggy-js';

export default function Index({ cotizaciones = [] }) {
  const [seleccionados, setSeleccionados] = useState([]);

  /* ===============================
     SELECCIÓN
  =============================== */
  const toggleSeleccion = (id) => {
    setSeleccionados((prev) =>
      prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
    );
  };

  const toggleSeleccionTodos = () => {
    if (seleccionados.length === cotizaciones.length) {
      setSeleccionados([]);
    } else {
      setSeleccionados(cotizaciones.map(c => c.id));
    }
  };

  /* ===============================
     ACCIONES
  =============================== */
  const reenviarCorreo = (id) => {
    if (confirm('¿Deseas reenviar la cotización por correo?')) {
      router.post(route('vendedor.cotizaciones.reenviar', id), {}, {
        onSuccess: () => alert('Correo reenviado exitosamente.')
      });
    }
  };

  const enviarLoteWhatsapp = () => {
    if (seleccionados.length === 0) {
      alert('Selecciona al menos una cotización.');
      return;
    }

    router.post(route('vendedor.cotizaciones.whatsapp-lote'), {
      ids: seleccionados,
    });
  };

  const enviarWhatsApp = (cot) => {
    const numero = `${cot.telefono || ''}`.replace(/\D/g, '');
    if (!numero || numero.length < 8) {
      alert('Número inválido');
      return;
    }

    const nombre = cot.nombre_cliente;
    const total = parseFloat(cot.total || 0).toFixed(2);
    const pdf = cot.drive_url || 'https://appleboss.bo/pdf-no-disponible';

    const mensaje =
      `Hola ${nombre}, gracias por confiar en *AppleBoss* 😊\n\n` +
      `📝 *Cotización AppleBoss*\n` +
      `👤 Cliente: ${nombre}\n` +
      `📄 Cotización N.º: ${cot.id}\n` +
      `💰 Total: Bs ${total}\n` +
      `🔗 Ver PDF: ${pdf}`;

    const encoded = encodeURIComponent(mensaje);
    const linkApp = `whatsapp://send?phone=${numero}&text=${encoded}`;
    const linkWeb = `https://web.whatsapp.com/send?phone=${numero}&text=${encoded}`;

    const timeout = setTimeout(() => {
      window.open(linkWeb, '_blank');
    }, 2000);

    window.location.href = linkApp;
    window.addEventListener('blur', () => clearTimeout(timeout), { once: true });
  };

  /* ===============================
     TOTALES
  =============================== */
  const calcularTotal = (campo, soloSeleccionados = false) => {
    return cotizaciones.reduce((acc, cot) => {
      const descuento = parseFloat(cot.descuento || 0);
      if (soloSeleccionados && !seleccionados.includes(cot.id)) return acc;
      if (!Array.isArray(cot.items)) return acc;

      const subtotal = cot.items.reduce((sum, item) => {
        const valor = parseFloat(item[campo]) || 0;
        return sum + valor * (item.cantidad || 1);
      }, 0);

      return acc + Math.max(0, subtotal - descuento);
    }, 0);
  };

  return (
    <VendedorLayout>
      <Head title="Cotizaciones" />

      {/* ===============================
         HEADER
      =============================== */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 className="text-2xl font-bold text-blue-700">
          Cotizaciones Registradas
        </h1>

        <div className="flex flex-col sm:flex-row gap-2">
          <Link href={route('vendedor.cotizaciones.create')}>
            <FancyButton size="sm" variant="success">
              Nueva Cotización
            </FancyButton>
          </Link>

          <FancyButton
            size="sm"
            variant="primary"
            onClick={enviarLoteWhatsapp}
          >
            Enviar seleccionados
          </FancyButton>
        </div>
      </div>

      {/* ===============================
         TABLA
      =============================== */}
      <div className="bg-white rounded-xl shadow overflow-x-auto">
        <table className="min-w-full text-sm text-left">
          <thead className="bg-gray-100 sticky top-0 z-10">
            <tr>
              <th className="px-4 py-3">
                <input
                  type="checkbox"
                  checked={seleccionados.length === cotizaciones.length && cotizaciones.length > 0}
                  onChange={toggleSeleccionTodos}
                />
              </th>
              <th className="px-2 py-3">#</th>
              <th className="px-2 py-3">Cliente</th>
              <th className="px-2 py-3">Teléfono</th>
              <th className="px-2 py-3">Correo</th>
              <th className="px-2 py-3 text-green-700 font-bold">TOTAL C/F</th>
              <th className="px-2 py-3 text-blue-700 font-bold">TOTAL S/F</th>
              <th className="px-2 py-3">Fecha</th>
              <th className="px-2 py-3 text-center">Acciones</th>
            </tr>
          </thead>

          <tbody>
            {cotizaciones.length === 0 ? (
              <tr>
                <td colSpan="9" className="text-center py-6 text-gray-500">
                  No hay cotizaciones registradas.
                </td>
              </tr>
            ) : (
              cotizaciones.map((cot, index) => {
                let totalSinFactura = 0;
                let totalConFactura = 0;
                const descuento = parseFloat(cot.descuento || 0);

                if (Array.isArray(cot.items)) {
                  cot.items.forEach(item => {
                    totalSinFactura += (item.precio_sin_factura || 0) * (item.cantidad || 1);
                    totalConFactura += (item.precio_con_factura || 0) * (item.cantidad || 1);
                  });
                }

                return (
                  <tr
                    key={cot.id}
                    className={`border-b hover:bg-blue-50 ${seleccionados.includes(cot.id) ? 'bg-blue-50 ring-1 ring-blue-100' : ''
                      }`}
                  >
                    <td className="px-4 py-3">
                      <input
                        type="checkbox"
                        checked={seleccionados.includes(cot.id)}
                        onChange={() => toggleSeleccion(cot.id)}
                      />
                    </td>
                    <td className="px-2 py-3">{index + 1}</td>
                    <td className="px-2 py-3">{cot.nombre_cliente}</td>
                    <td className="px-2 py-3">{cot.telefono || '-'}</td>
                    <td className="px-2 py-3">{cot.correo_cliente || '-'}</td>
                    <td className="px-2 py-3 text-green-700 font-semibold">
                      Bs {(Math.max(0, totalConFactura - descuento)).toFixed(2)}
                    </td>
                    <td className="px-2 py-3 text-blue-700 font-semibold">
                      Bs {(Math.max(0, totalSinFactura - descuento)).toFixed(2)}
                    </td>
                    <td className="px-2 py-3">
                      {new Date(cot.fecha_cotizacion).toLocaleDateString()}
                    </td>
                    <td className="px-2 py-3">
                      <div className="flex flex-wrap justify-center gap-1">
                        {cot.drive_url ? (
                          <a href={cot.drive_url} target="_blank">
                            <FancyButton size="sm" variant="primary">
                              Ver PDF
                            </FancyButton>
                          </a>
                        ) : (
                          <a href={route('vendedor.cotizaciones.pdf', cot.id)} target="_blank">
                            <FancyButton size="sm" variant="primary">
                              Generar PDF
                            </FancyButton>
                          </a>
                        )}

                        <FancyButton
                          size="sm"
                          variant="success"
                          onClick={() => enviarWhatsApp(cot)}
                        >
                          WhatsApp
                        </FancyButton>

                        {cot.correo_cliente && (
                          <FancyButton
                            size="sm"
                            variant="dark"
                            onClick={() => reenviarCorreo(cot.id)}
                          >
                            Reenviar
                          </FancyButton>
                        )}
                      </div>
                    </td>
                  </tr>
                );
              })
            )}
          </tbody>
        </table>

        {/* ===============================
           FOOTER TOTALES
        =============================== */}
        <div className="bg-gray-50 px-6 py-4 border-t text-sm font-medium flex flex-col sm:flex-row sm:justify-end gap-3">
          <div className="text-green-700">
            Total Global C/F:{' '}
            <strong>Bs {calcularTotal('precio_con_factura').toFixed(2)}</strong>
          </div>
          <div className="text-blue-700">
            Total Global S/F:{' '}
            <strong>Bs {calcularTotal('precio_sin_factura').toFixed(2)}</strong>
          </div>

          {seleccionados.length > 0 && (
            <>
              <div className="text-green-700">
                Sel. C/F:{' '}
                <strong>Bs {calcularTotal('precio_con_factura', true).toFixed(2)}</strong>
              </div>
              <div className="text-blue-700">
                Sel. S/F:{' '}
                <strong>Bs {calcularTotal('precio_sin_factura', true).toFixed(2)}</strong>
              </div>
            </>
          )}
        </div>
      </div>
    </VendedorLayout>
  );
}
