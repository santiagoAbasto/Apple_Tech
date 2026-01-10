import { Link, Head, useForm } from '@inertiajs/react';

export default function AdminLayout({ children }) {
  const { post } = useForm();
  const pathname = window.location.pathname;

  const handleLogout = (e) => {
    e.preventDefault();
    post(route('logout'));
  };

  return (
    <>
      <Head title="Panel Admin | Apple Boss" />

      {/* SB ADMIN ASSETS */}
      <link rel="stylesheet" href="/sbadmin/vendor/fontawesome-free/css/all.min.css" />
      <link rel="stylesheet" href="/sbadmin/css/sb-admin-2.min.css" />

      <script src="/sbadmin/vendor/jquery/jquery.min.js" defer></script>
      <script src="/sbadmin/vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>
      <script src="/sbadmin/js/sb-admin-2.min.js" defer></script>

      {/* WRAPPER GENERAL */}
      <div className="d-flex bg-gray-100" style={{ minHeight: '100vh' }}>

        {/* SIDEBAR */}
        <aside
          className="bg-gradient-primary sidebar sidebar-dark accordion position-fixed"
          id="accordionSidebar"
          style={{
            width: '224px',
            height: '100vh',
            overflowY: 'auto',
          }}
        >
          {/* LOGO */}
          <Link
            className="sidebar-brand d-flex align-items-center justify-content-center"
            href={route('admin.dashboard')}
          >
            <div className="sidebar-brand-icon rotate-n-15">
              <i className="fas fa-mobile-alt"></i>
            </div>
            <div className="sidebar-brand-text mx-3">Apple Tech Adminn</div>
          </Link>

          <hr className="sidebar-divider my-0" />

          {/* DASHBOARD */}
          <li className="nav-item">
            <Link className="nav-link" href={route('admin.dashboard')}>
              <i className="fas fa-fw fa-tachometer-alt me-2"></i>
              <span>Dashboard</span>
            </Link>
          </li>

          <hr className="sidebar-divider" />
          <div className="sidebar-heading px-3">Inventario</div>

          {[
            { route: 'admin.celulares.index', icon: 'fa-mobile', label: 'Celulares' },
            { route: 'admin.computadoras.index', icon: 'fa-laptop', label: 'Computadoras' },
            { route: 'admin.productos-apple.index', icon: 'fa-apple-alt', label: 'Productos Apple' },
            { route: 'admin.productos-generales.index', icon: 'fa-box', label: 'Productos Generales' },
          ].map(({ route: r, icon, label }) => (
            <li className="nav-item" key={label}>
              <Link className="nav-link" href={route(r)}>
                <i className={`fas ${icon} me-2`}></i>
                <span>{label}</span>
              </Link>
            </li>
          ))}

          <hr className="sidebar-divider" />
          <div className="sidebar-heading px-3">Operaciones</div>

          {[
            { route: 'admin.ventas.index', icon: 'fa-shopping-cart', label: 'Ventas' },
            { route: 'admin.servicios.index', icon: 'fa-tools', label: 'Servicio Técnico' },
            { route: 'admin.reportes.index', icon: 'fa-chart-line', label: 'Reportes' },
            { route: 'admin.cotizaciones.index', icon: 'fa-file-invoice-dollar', label: 'Cotizaciones' },
            { route: 'admin.egresos.index', icon: 'fa-hand-holding-usd', label: 'Egresos' },
          ].map(({ route: r, icon, label }) => (
            <li className="nav-item" key={label}>
              <Link className="nav-link" href={route(r)}>
                <i className={`fas ${icon} me-2`}></i>
                <span>{label}</span>
              </Link>
            </li>
          ))}

          <hr className="sidebar-divider" />
          <div className="sidebar-heading px-3">Otros</div>

          <li className="nav-item">
            <Link className="nav-link" href={route('admin.exportaciones.index')}>
              <i className="fas fa-file-export me-2"></i>
              <span>Exportaciones</span>
            </Link>
          </li>

          <li className="nav-item">
            <Link
              className={`nav-link ${pathname.startsWith('/admin/clientes') ? 'active bg-white text-dark fw-bold' : ''}`}
              href="/admin/clientes"
            >
              <i className="fas fa-users me-2"></i>
              <span>Mis Clientes</span>
            </Link>
          </li>

          <hr className="sidebar-divider" />

          {/* LOGOUT */}
          <li className="nav-item mb-3">
            <a href="#" className="nav-link" onClick={handleLogout}>
              <i className="fas fa-sign-out-alt me-2"></i>
              <span>Cerrar sesión</span>
            </a>
          </li>
        </aside>

        {/* CONTENT */}
        <main
          className="flex-grow-1"
          style={{
            marginLeft: '224px',
            height: '100vh',
            overflowY: 'auto',
          }}
        >
          <div className="container-fluid py-4 px-4">
            <div className="bg-white shadow-sm rounded p-4">
              {children}
            </div>
          </div>
        </main>
      </div>
    </>
  );
}
