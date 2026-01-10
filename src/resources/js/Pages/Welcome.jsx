import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Apple_Tech | Gestión Técnica Profesional" />

            <div className="relative min-h-screen overflow-hidden bg-black text-white">

                {/* BACKGROUND GLOW */}
                <div className="absolute inset-0 pointer-events-none">
                    <div className="absolute -top-40 -left-40 h-[32rem] w-[32rem] bg-red-600/20 blur-[140px] rounded-full" />
                    <div className="absolute top-1/3 right-0 h-[28rem] w-[28rem] bg-red-500/10 blur-[120px] rounded-full" />
                </div>

                {/* NAVBAR */}
                <header className="relative z-10 max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
                    
                    {/* LOGO */}
                    <div className="flex items-center gap-3">
                        <img
                            src="/images/logo-appletech.jpeg"
                            alt="Apple_Tech"
                            className="h-10 w-10 rounded-xl object-cover shadow-lg"
                        />
                        <span className="text-xl font-bold tracking-wide">
                            Apple<span className="text-red-500">_Tech</span>
                        </span>
                    </div>

                    {/* NAV */}
                    <nav className="flex items-center gap-4">
                        {auth?.user ? (
                            <Link
                                href={route('dashboard')}
                                className="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 transition font-medium"
                            >
                                Ir al Dashboard
                            </Link>
                        ) : (
                            <>
                                <Link
                                    href={route('login')}
                                    className="px-5 py-2.5 rounded-xl border border-white/20 hover:bg-white/10 transition"
                                >
                                    Iniciar sesión
                                </Link>
                                <Link
                                    href={route('register')}
                                    className="px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 transition font-medium"
                                >
                                    Registrarse
                                </Link>
                            </>
                        )}
                    </nav>
                </header>

                {/* HERO */}
                <main className="relative z-10 max-w-7xl mx-auto px-6 pt-28 pb-24">
                    <motion.div
                        initial={{ opacity: 0, y: 40 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.8 }}
                        className="max-w-3xl"
                    >
                        <h1 className="text-5xl md:text-6xl font-extrabold leading-tight">
                            Gestión profesional para tu
                            <span className="block text-red-500 mt-2">
                                negocio Apple
                            </span>
                        </h1>

                        <p className="mt-6 text-lg text-white/70 leading-relaxed">
                            Apple_Tech centraliza ventas, inventario, servicios técnicos,
                            cotizaciones y reportes en una sola plataforma moderna,
                            segura y escalable.
                        </p>

                        <div className="mt-10 flex flex-wrap gap-4">
                            <Link
                                href={route('login')}
                                className="px-7 py-3.5 rounded-2xl bg-red-600 hover:bg-red-700 transition text-lg font-semibold"
                            >
                                Acceder al sistema
                            </Link>

                            <a
                                href="#features"
                                className="px-7 py-3.5 rounded-2xl border border-white/20 hover:bg-white/10 transition text-lg"
                            >
                                Ver funcionalidades
                            </a>
                        </div>
                    </motion.div>
                </main>

                {/* FEATURES */}
                <section
                    id="features"
                    className="relative z-10 max-w-7xl mx-auto px-6 pb-28"
                >
                    <div className="grid md:grid-cols-3 gap-8">
                        {[
                            {
                                title: 'Ventas y Servicios',
                                desc: 'Ventas múltiples, servicios técnicos, control de ganancias y boletas profesionales.'
                            },
                            {
                                title: 'Inventario Especializado',
                                desc: 'Gestión de equipos Apple, control por IMEI, series, estados y stock real.'
                            },
                            {
                                title: 'Reportes y Control',
                                desc: 'Dashboards, gráficos, PDFs automáticos y control financiero en tiempo real.'
                            }
                        ].map((item, index) => (
                            <motion.div
                                key={index}
                                initial={{ opacity: 0, y: 30 }}
                                whileInView={{ opacity: 1, y: 0 }}
                                viewport={{ once: true }}
                                transition={{ delay: index * 0.15 }}
                                className="rounded-2xl bg-white/5 border border-white/10 p-7 backdrop-blur hover:border-red-500/40 hover:bg-white/10 transition"
                            >
                                <h3 className="text-xl font-semibold mb-3">
                                    {item.title}
                                </h3>
                                <p className="text-white/70 leading-relaxed">
                                    {item.desc}
                                </p>
                            </motion.div>
                        ))}
                    </div>
                </section>

                {/* CTA FINAL */}
                <section className="relative z-10 pb-24 text-center">
                    <motion.div
                        initial={{ opacity: 0, scale: 0.96 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6 }}
                    >
                        <h2 className="text-4xl font-bold">
                            Digitalizá tu negocio hoy
                        </h2>
                        <p className="mt-4 text-white/70 max-w-xl mx-auto">
                            Apple_Tech está diseñado para crecer contigo y adaptarse
                            a la realidad de tu negocio técnico.
                        </p>

                        <Link
                            href={route('register')}
                            className="inline-block mt-8 px-9 py-4 rounded-2xl bg-red-600 hover:bg-red-700 transition text-lg font-semibold"
                        >
                            Comenzar ahora
                        </Link>
                    </motion.div>
                </section>

                {/* FOOTER */}
                <footer className="relative z-10 text-center py-6 text-white/40 text-sm">
                    © {new Date().getFullYear()} Apple_Tech · Sistema de Gestión Técnica
                </footer>
            </div>
        </>
    );
}
