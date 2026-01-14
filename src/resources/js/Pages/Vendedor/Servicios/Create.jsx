import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';
import axios from 'axios';
import VendedorLayout from '@/Layouts/VendedorLayout';
import { route } from 'ziggy-js';

/* ======================
   ESTILOS BASE
====================== */
const inputStyle = `
  w-full rounded-lg 
  bg-slate-900/60
  border border-slate-700 
  px-3 py-2 text-sm text-slate-200
  focus:border-blue-500 focus:ring-2 focus:ring-blue-500/30
  transition-all outline-none
  placeholder:text-slate-500
`;

const btnBase = `
  inline-flex items-center justify-center gap-2
  font-semibold transition-all duration-150
  active:scale-95 disabled:opacity-50
`;

export default function CreateServicio() {
  const { data, setData, post, processing, errors } = useForm({
    cliente: '',
    telefono: '',
    equipo: '',
    tecnico: '',
    fecha: new Date().toISOString().split('T')[0],
    detalle_servicio: '',
    notas_adicionales: '',
    precio_costo: 0,
    precio_venta: 0,
  });

  /* ======================
     SERVICIOS (ESTRUCTURA REAL)
  ====================== */
  const [servicios, setServicios] = useState([
    { descripcion: '', costo: '', precio: '' },
  ]);

  /* ======================
     CLIENTES (AUTOCOMPLETE)
  ====================== */
  const [sugerencias, setSugerencias] = useState([]);
  const [mostrarSugerencias, setMostrarSugerencias] = useState(false);

  const buscarCliente = async (valor) => {
    setData('cliente', valor);
    if (valor.length < 2) return setMostrarSugerencias(false);

    const res = await axios.get(
      route('vendedor.clientes.sugerencias', { term: valor })
    );

    setSugerencias(res.data);
    setMostrarSugerencias(true);
  };

  const seleccionarCliente = (c) => {
    setData((prev) => ({
      ...prev,
      cliente: c.nombre,
      telefono: c.telefono,
    }));
    setMostrarSugerencias(false);
  };

  /* ======================
     SERVICIOS CRUD
  ====================== */
  const agregarServicio = () =>
    setServicios((prev) => [
      ...prev,
      { descripcion: '', costo: '', precio: '' },
    ]);

  const actualizarServicio = (i, campo, valor) =>
    setServicios((prev) =>
      prev.map((s, idx) => (idx === i ? { ...s, [campo]: valor } : s))
    );

  const eliminarServicio = (i) =>
    setServicios((prev) => prev.filter((_, idx) => idx !== i));

  /* ======================
     TOTALES REALES
  ====================== */
  const totalCosto = servicios.reduce(
    (sum, s) => sum + Number(s.costo || 0),
    0
  );

  const totalVenta = servicios.reduce(
    (sum, s) => sum + Number(s.precio || 0),
    0
  );

  /* ======================
     SUBMIT (JSON LIMPIO)
  ====================== */
  const handleSubmit = (e) => {
    e.preventDefault();

    const serviciosValidos = servicios.filter(
      (s) =>
        s.descripcion &&
        Number(s.costo) >= 0 &&
        Number(s.precio) >= 0
    );

    if (serviciosValidos.length === 0) {
      alert('Debe registrar al menos un servicio válido');
      return;
    }

    if (!data.tecnico.trim()) {
      alert('Debe indicar el técnico encargado');
      return;
    }

    const detalleJSON = serviciosValidos.map((s) => ({
      descripcion: s.descripcion,
      costo: Number(s.costo),
      precio: Number(s.precio),
    }));

    // ✅ CLAVE: setData PRIMERO
    setData({
      ...data,
      detalle_servicio: JSON.stringify(detalleJSON),
      precio_costo: totalCosto,
      precio_venta: totalVenta,
    });

    // ✅ post SIN payload
    post(route('vendedor.servicios.store'));
  };



  return (
    <VendedorLayout>
      <Head title="Registrar Servicio Técnico" />

      <div className="max-w-5xl mx-auto px-5 py-6 space-y-6">

        {/* HEADER */}
        <header className="rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-500 p-5 text-white shadow-md">
          <h1 className="text-xl font-bold">Servicio Técnico</h1>
          <p className="text-sm opacity-90">
            Registro profesional de trabajos técnicos
          </p>
        </header>

        <form onSubmit={handleSubmit} className="space-y-6">

          {/* CLIENTE */}
          <section className="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h2 className="text-[11px] font-bold uppercase tracking-widest text-blue-400 mb-4">
              Información del Cliente
            </h2>

            <div className="grid md:grid-cols-2 gap-4">
              <div className="relative">
                <label className="text-xs text-slate-400 mb-1 block">
                  Cliente
                </label>
                <input
                  value={data.cliente}
                  onChange={(e) => buscarCliente(e.target.value)}
                  onBlur={() => setTimeout(() => setMostrarSugerencias(false), 150)}
                  className={inputStyle}
                  placeholder="Nombre del cliente"
                />

                {mostrarSugerencias && (
                  <ul className="absolute z-20 w-full mt-1 bg-slate-900 border border-slate-700 rounded-lg">
                    {sugerencias.map((c, i) => (
                      <li
                        key={i}
                        onClick={() => seleccionarCliente(c)}
                        className="px-3 py-2 text-sm hover:bg-blue-600 cursor-pointer flex justify-between"
                      >
                        <span>{c.nombre}</span>
                        <span className="text-slate-400">{c.telefono}</span>
                      </li>
                    ))}
                  </ul>
                )}
              </div>

              {['telefono', 'equipo', 'tecnico'].map((f) => (
                <div key={f}>
                  <label className="text-xs text-slate-400 mb-1 block capitalize">
                    {f}
                  </label>
                  <input
                    value={data[f]}
                    onChange={(e) => setData(f, e.target.value)}
                    className={inputStyle}
                  />
                </div>
              ))}
            </div>
          </section>

          {/* SERVICIOS */}
          <section className="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-[11px] font-bold uppercase tracking-widest text-blue-400">
                Detalle del Servicio
              </h2>
              <button
                type="button"
                onClick={agregarServicio}
                className={`${btnBase} px-3 py-1.5 text-xs rounded-lg bg-blue-500/10 text-blue-400 hover:bg-blue-500 hover:text-white`}
              >
                + Agregar ítem
              </button>
            </div>

            <div className="space-y-3">
              {servicios.map((s, i) => (
                <div
                  key={i}
                  className="grid md:grid-cols-12 gap-3 bg-slate-800/40 p-3 rounded-xl"
                >
                  <div className="md:col-span-6">
                    <label className="text-[10px] uppercase text-slate-400">
                      Servicio
                    </label>
                    <input
                      value={s.descripcion}
                      onChange={(e) =>
                        actualizarServicio(i, 'descripcion', e.target.value)
                      }
                      className={inputStyle}
                    />
                  </div>

                  <div className="md:col-span-3">
                    <label className="text-[10px] uppercase text-slate-400">
                      Costo
                    </label>
                    <input
                      type="number"
                      min="0"
                      value={s.costo}
                      onChange={(e) =>
                        actualizarServicio(i, 'costo', e.target.value)
                      }
                      className={inputStyle}
                    />
                  </div>

                  <div className="md:col-span-2">
                    <label className="text-[10px] uppercase text-slate-400">
                      Precio Cliente
                    </label>
                    <input
                      type="number"
                      min="0"
                      value={s.precio}
                      onChange={(e) =>
                        actualizarServicio(i, 'precio', e.target.value)
                      }
                      className={inputStyle}
                    />
                  </div>

                  <div className="md:col-span-1 flex items-end">
                    <button
                      type="button"
                      onClick={() => eliminarServicio(i)}
                      className="w-full h-9 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-500/10 transition"
                    >
                      ✕
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </section>

          {/* NOTAS */}
          <section className="bg-slate-900 border border-slate-800 rounded-2xl p-5">
            <h2 className="text-[11px] font-bold uppercase tracking-widest text-blue-400 mb-3">
              Notas adicionales
            </h2>
            <textarea
              rows={3}
              value={data.notas_adicionales}
              onChange={(e) => setData('notas_adicionales', e.target.value)}
              placeholder="Observaciones, condiciones, recomendaciones..."
              className={inputStyle}
            />
          </section>

          {/* FOOTER */}
          <div className="flex justify-between items-center">
            <div className="text-sm text-slate-300">
              <p>
                Costo total:{' '}
                <strong>Bs {totalCosto.toFixed(2)}</strong>
              </p>
              <p>
                Cliente paga:{' '}
                <strong className="text-green-400">
                  Bs {totalVenta.toFixed(2)}
                </strong>
              </p>
            </div>

            <button
              type="submit"
              disabled={processing}
              className={`${btnBase} px-8 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-white text-sm shadow-md`}
            >
              {processing ? 'Guardando…' : 'Guardar Servicio'} →
            </button>
          </div>
        </form>
      </div>
    </VendedorLayout>
  );
}
