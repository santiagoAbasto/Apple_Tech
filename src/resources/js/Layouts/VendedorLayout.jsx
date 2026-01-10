import { Head, Link, usePage, router } from '@inertiajs/react';

export default function VendedorLayout({ children }) {
  const { auth } = usePage().props;
  const pathname = window.location.pathname;

  const handleLogout = () => {
    router.post(route('logout'));
  };

  return (
    <>
      <Head title="Panel Vendedor | Apple Tech" />

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
          className="bg-gradient-success sidebar sidebar-dark accordion position-fixed"
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
            href="/vendedor/dashboard"
          >
            <div className="sidebar-brand-icon">
              <i className="fas fa-store"></i>
            </div>
            <div className="sidebar-brand-text mx-3">AppleBoss</div>
          </Link>

          <hr className="sidebar-divider my-0" />

          {[
            {
              href: '/vendedor/dashboard',
              icon: 'fa-chart-line',
              label: 'Panel Principal',
            },
            {
              href: '/vendedor/productos',
              icon: 'fa-box-open',
              label: 'Ver Productos',
            },
            {
              href: '/vendedor/ventas',
              icon: 'fa-file-invoice-dollar',
              label: 'Mis Ventas',
              active: pathname.startsWith('/vendedor/ventas') && !pathname.includes('/create'),
            },
            {
              href: '/vendedor/ventas/create',
              icon: 'fa-receipt',
              label: 'Registrar Venta',
            },
            {
              href: '/vendedor/cotizaciones',
              icon: 'fa-file-alt',
              label: 'Mis Cotizaciones',
            },
            {
              href: '/vendedor/servicios',
              icon: 'fa-tools',
              label: 'Servicio Técnico',
            },
            {
              href: '/vendedor/clientes',
              icon: 'fa-users',
              label: 'Mis Clientes',
            },
          ].map(({ href, icon, label, active }) => (
            <li className="nav-item" key={label}>
              <Link
                href={href}
                className={`nav-link ${
                  active || pathname.startsWith(href) ? 'active fw-bold' : ''
                }`}
              >
                <i className={`fas ${icon} me-2`}></i>
                <span>{label}</span>
              </Link>
            </li>
          ))}
        </aside>

        {/* CONTENT */}
        <main
          className="flex-grow-1 d-flex flex-column"
          style={{
            marginLeft: '224px',
            minHeight: '100vh',
          }}
        >
          {/* TOPBAR */}
          <nav className="navbar navbar-expand navbar-light bg-white shadow-sm px-4">
            <h6 className="font-weight-bold text-success mt-2 mb-0">
              Panel del Vendedor
            </h6>

            <div className="ms-auto d-flex align-items-center gap-3">
              <span className="text-dark small fw-bold">
                {auth?.user?.name}
              </span>

              <button
                className="btn btn-sm btn-danger shadow-sm"
                onClick={handleLogout}
              >
                <i className="fas fa-sign-out-alt me-1"></i>
                Cerrar Sesión
              </button>
            </div>
          </nav>

          {/* MAIN CONTENT (SCROLL AQUÍ) */}
          <section
            className="flex-grow-1"
            style={{ overflowY: 'auto' }}
          >
            <div className="container-fluid py-4">
              {children}
            </div>
          </section>

          {/* FOOTER */}
          <footer className="bg-white border-top py-3 text-center small">
            © Apple Tech 2026 · Todos los derechos reservados
          </footer>
        </main>
      </div>
    </>
  );
}
