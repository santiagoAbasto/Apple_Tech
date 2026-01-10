// resources/js/Components/EconomicCharts.jsx
import Chart from 'react-apexcharts';

export default function EconomicCharts({ resumen_total = {} }) {
  /* =======================
     HELPERS INTERNOS (ROBUSTOS)
  ======================= */
  const toNumber = (n) => {
    const num = Number(n);
    return isNaN(num) ? 0 : num;
  };

  // 🔒 Normaliza valores inflados (centavos ×100)
  const normalize = (value) => {
    const num = toNumber(value);
    return Math.abs(num) > 10000 ? num / 100 : num;
  };

  const fmtBs = (n) =>
    `Bs ${normalize(n).toLocaleString('es-BO', {
      minimumFractionDigits: 2,
    })}`;

  /* =======================
     BAR CHART (visión ejecutiva)
  ======================= */
  const ingresos = normalize(resumen_total.total_ventas);
  const inversion =
    normalize(resumen_total.total_costo) +
    normalize(resumen_total.total_permuta);

  const utilidad = normalize(resumen_total.utilidad_disponible);

  return (
    <div className="bg-white rounded-xl shadow p-4">
      <h3 className="font-semibold text-gray-700 mb-3">
        📈 Ingresos vs Inversión vs Utilidad
      </h3>

      <Chart
        type="bar"
        height={320}
        series={[
          {
            name: 'Monto (Bs)',
            data: [ingresos, inversion, utilidad],
          },
        ]}
        options={{
          chart: {
            toolbar: { show: false },
            animations: { enabled: true },
          },
          xaxis: {
            categories: [
              'Ingresos',
              'Inversión (Costo + Permuta)',
              'Utilidad Disponible',
            ],
          },
          plotOptions: {
            bar: {
              borderRadius: 6,
              columnWidth: '45%',
            },
          },
          dataLabels: { enabled: false },
          tooltip: {
            y: {
              formatter: (val) => fmtBs(val),
            },
          },
          colors: [
            '#0284c7', // ingresos
            '#6366f1', // inversión
            utilidad < 0 ? '#e11d48' : '#16a34a', // utilidad
          ],
          yaxis: {
            labels: {
              formatter: (val) =>
                `Bs ${val.toLocaleString('es-BO')}`,
            },
          },
        }}
      />
    </div>
  );
}
