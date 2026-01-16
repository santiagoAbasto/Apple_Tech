import { Link, Head, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { route } from 'ziggy-js';
import ConfirmLogoutModal from '@/Components/ConfirmLogoutModal';


export default function AdminLayout({ children }) {
  const { post } = useForm();
  const pathname = window.location.pathname;

  const [sidebarOpen, setSidebarOpen] = useState(false);
  const [isDesktop, setIsDesktop] = useState(
    typeof window !== 'undefined' ? window.innerWidth >= 768 : true
  );
  const [showLogoutModal, setShowLogoutModal] = useState(false);


  /* ===============================
     RESPONSIVE BREAKPOINT
  =============================== */
  useEffect(() => {
    const handleResize = () => {
      const desktop = window.innerWidth >= 768;
      setIsDesktop(desktop);

      if (desktop) {
        setSidebarOpen(false);
      }
    };

    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  const handleLogout = (e) => {
    e.preventDefault();
    setShowLogoutModal(true);
  };

  return (
    <>
      <Head title="Panel Admin | Apple Boss" />

      {/* SB ADMIN ASSETS */}
      <link
        rel="stylesheet"
        href="/sbadmin/vendor/fontawesome-free/css/all.min.css"
      />
      <link rel="stylesheet" href="/sbadmin/css/sb-admin-2.min.css" />

      <div className="d-flex bg-gray-100" style={{ minHeight: '100vh' }}>

        {/* OVERLAY MOBILE */}
        {sidebarOpen && !isDesktop && (
          <div
            className="position-fixed w-100 h-100 bg-dark"
            style={{ opacity: 0.5, zIndex: 1040 }}
            onClick={() => setSidebarOpen(false)}
          />
        )}

        {/* SIDEBAR */}
        <aside
          className="bg-gradient-primary sidebar sidebar-dark accordion"
          style={{
            width: '224px',
            height: '100vh',
            overflowY: 'auto',
            position: 'fixed',
            zIndex: 1050,
            left: isDesktop ? '0' : sidebarOpen ? '0' : '-224px',
            transition: 'left 0.25s ease',
          }}
        >
          {/* LOGO */}
          <Link
            className="sidebar-brand d-flex align-items-center justify-content-center"
            href={route('admin.dashboard')}
            onClick={() => setSidebarOpen(false)}
          >
            <div className="sidebar-brand-icon rotate-n-15">
              <i className="fas fa-mobile-alt"></i>
            </div>
            <div className="sidebar-brand-text mx-3">
              Apple Tech Admin
            </div>
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
          <div className="sidebar-heading px-3">Clientes</div>

          {/* CLIENTES (NO SE BORRA) */}
          <li className="nav-item">
            <Link
              className={`nav-link ${pathname.startsWith('/admin/clientes')
                ? 'active bg-white text-dark fw-bold'
                : ''
                }`}
              href={route('admin.clientes.index')}
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
          <ConfirmLogoutModal
            open={showLogoutModal}
            onClose={() => setShowLogoutModal(false)}
            onConfirm={() => post(route('logout'))}
          />
        </aside>

        {/* CONTENT */}
        <main
          className="flex-grow-1"
          style={{
            marginLeft: isDesktop ? '224px' : '0',
            minHeight: '100vh',
            width: '100%',
          }}
        >
          {/* TOPBAR MOBILE */}
          {!isDesktop && (
            <div className="bg-white shadow-sm px-3 py-2 d-flex align-items-center">
              <button
                className="btn btn-link text-dark"
                onClick={() => setSidebarOpen(true)}
              >
                <i className="fas fa-bars fa-lg"></i>
              </button>
              <span className="ms-2 fw-bold">Apple Boss</span>
            </div>
          )}

          {/* PAGE CONTENT */}
          <div className="container-fluid px-3 px-md-4 py-3">
            <div className="bg-white shadow-sm rounded p-4">
              {children}
            </div>
          </div>
        </main>
      </div>
    </>
  );
}
