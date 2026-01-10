import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { motion } from 'framer-motion';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('password.email'));
    };

    return (
        <GuestLayout>
            <Head title="Recuperar contraseña | Apple_Tech" />

            {/* CONTENEDOR PRINCIPAL */}
            <div className="
                relative min-h-screen flex items-center justify-center
                bg-gradient-to-br from-black via-zinc-900 to-black
                px-8
            ">
                {/* GLOW */}
                <div className="absolute inset-0 pointer-events-none">
                    <div className="absolute -top-40 left-1/4 h-[420px] w-[420px] bg-red-600/20 blur-3xl rounded-full" />
                    <div className="absolute bottom-0 right-1/4 h-[420px] w-[420px] bg-red-500/10 blur-3xl rounded-full" />
                </div>

                {/* CONTEXTO */}
                <div className="absolute top-6 left-6 text-white/40 text-sm tracking-wide">
                    Apple_Tech · Seguridad de acceso
                </div>

                {/* CARD */}
                <motion.div
                    initial={{ opacity: 0, y: 28 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.45, ease: 'easeOut' }}
                    className="
                        relative z-10
                        w-full max-w-lg
                        rounded-2xl
                        bg-zinc-900/95
                        border border-white/10
                        px-12 py-10
                        shadow-[0_20px_60px_rgba(0,0,0,0.65)]
                    "
                >
                    {/* HEADER */}
                    <div className="mb-8 text-center">
                        <div className="flex justify-center mb-4">
                            <img
                                src="/images/logo-appletech.jpeg"
                                alt="Apple_Tech"
                                className="h-14 w-14 rounded-xl object-cover shadow-lg"
                            />
                        </div>

                        <h1 className="text-3xl font-semibold tracking-wide text-white">
                            Recuperar contraseña
                        </h1>
                        <p className="mt-2 text-sm text-white/60">
                            Te enviaremos un enlace seguro a tu correo
                        </p>
                    </div>

                    {/* INFO */}
                    <div className="mb-6 text-sm text-white/70 leading-relaxed">
                        ¿Olvidaste tu contraseña? Ingresá tu correo electrónico
                        registrado y recibirás un enlace para restablecerla
                        de forma segura.
                    </div>

                    {/* STATUS */}
                    {status && (
                        <div className="mb-6 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                            {status}
                        </div>
                    )}

                    {/* FORM */}
                    <form onSubmit={submit} className="space-y-6">
                        <div>
                            <TextInput
                                id="email"
                                type="email"
                                name="email"
                                value={data.email}
                                className="
                                    mt-1 w-full rounded-xl
                                    bg-black/40
                                    border border-white/10
                                    text-white
                                    focus:border-red-500
                                    focus:ring-red-500
                                "
                                isFocused
                                placeholder="Correo electrónico"
                                onChange={(e) =>
                                    setData('email', e.target.value)
                                }
                            />

                            <InputError message={errors.email} className="mt-2" />
                        </div>

                        <PrimaryButton
                            disabled={processing}
                            className="
                                w-full justify-center rounded-xl
                                bg-red-600 hover:bg-red-700
                                py-3 text-lg font-medium
                                transition
                            "
                        >
                            {processing
                                ? 'Enviando enlace…'
                                : 'Enviar enlace de recuperación'}
                        </PrimaryButton>
                    </form>

                    {/* FOOTER */}
                    <p className="mt-10 text-center text-xs text-white/40">
                        © {new Date().getFullYear()} Apple_Tech · Seguridad y confianza
                    </p>
                </motion.div>
            </div>
        </GuestLayout>
    );
}
