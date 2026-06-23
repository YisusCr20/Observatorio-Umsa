<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg: #f4f8ff;
            --surface: #ffffff;
            --surface-soft: #eef4ff;
            --border: rgba(15, 23, 42, 0.12);
            --text: #0f172a;
            --muted: #64748b;
            --primary: #2563eb;
            --primary-soft: rgba(37, 99, 235, 0.12);
            --accent: #d97706;
            --success: #16a34a;
            --shadow: rgba(15, 23, 42, 0.18);
        }

        [data-theme="dark"] {
            --bg: #07111f;
            --surface: #101b2d;
            --surface-soft: #17243a;
            --border: rgba(255, 255, 255, 0.10);
            --text: #f8fafc;
            --muted: #94a3b8;
            --primary: #3b82f6;
            --primary-soft: rgba(59, 130, 246, 0.18);
            --accent: #fbbf24;
            --success: #22c55e;
            --shadow: rgba(0, 0, 0, 0.55);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
        }

        .auth-bg {
            background:
                radial-gradient(circle at 15% 20%, var(--primary-soft), transparent 34%),
                radial-gradient(circle at 85% 80%, rgba(251, 191, 36, 0.13), transparent 34%),
                linear-gradient(135deg, var(--bg) 0%, var(--surface-soft) 50%, var(--bg) 100%);
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid var(--border);
            box-shadow: 0 30px 100px var(--shadow);
            color: var(--text);
        }

        [data-theme="dark"] .auth-card {
            background: rgba(15, 23, 42, 0.92);
        }

        .side-panel {
            background:
                radial-gradient(circle at top left, var(--primary-soft), transparent 40%),
                linear-gradient(135deg, rgba(37, 99, 235, 0.22), transparent);
            border-right: 1px solid var(--border);
        }

        .input-box {
            background: var(--surface-soft);
            border: 1px solid var(--border);
            color: var(--text);
            transition: all .25s ease;
        }

        .input-box::placeholder {
            color: var(--muted);
            opacity: .75;
        }

        .input-box:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-soft);
            outline: none;
        }

        .muted {
            color: var(--muted);
        }

        .primary-text {
            color: var(--primary);
        }

        .accent-text {
            color: var(--accent);
        }

        .primary-button {
            background: linear-gradient(135deg, var(--primary), #0ea5e9);
            color: white;
            box-shadow: 0 18px 45px var(--primary-soft);
        }

        .glass-button {
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid var(--border);
            color: var(--text);
            backdrop-filter: blur(20px);
        }

        .icon-box {
            background: var(--primary-soft);
            border: 1px solid rgba(59, 130, 246, 0.28);
            color: var(--primary);
        }

        @media (max-height: 700px) and (min-width: 1024px) {
            .compact-desktop {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }

            .compact-input {
                padding-top: .68rem !important;
                padding-bottom: .68rem !important;
            }
        }
    </style>

    <div class="min-h-[100dvh] auth-bg relative overflow-x-hidden flex items-center justify-center px-3 pt-16 pb-5 sm:px-6 sm:py-6 lg:px-8">

        <!-- Botón volver -->
        <div class="fixed top-4 left-4 z-50">
            <a href="{{ route('bienvenido') }}"
               class="group flex items-center gap-2 muted hover:opacity-100 opacity-80 transition-all duration-300">
                <div class="w-10 h-10 rounded-full glass-button flex items-center justify-center group-hover:scale-105 transition">
                    <i class="fas fa-arrow-left text-xs"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest hidden sm:block">
                    Volver
                </span>
            </a>
        </div>

        <!-- Botón tema -->
        <button type="button"
                onclick="toggleTheme()"
                class="fixed top-4 right-4 z-50 w-10 h-10 rounded-full glass-button flex items-center justify-center hover:scale-105 transition"
                title="Cambiar tema">
            <i id="themeIcon" class="fas fa-moon text-sm primary-text"></i>
        </button>

        <!-- Decoración -->
        <div class="absolute top-24 left-8 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-8 w-52 h-52 bg-yellow-400/10 rounded-full blur-3xl"></div>

        <!-- Card principal -->
        <div class="relative z-10 w-full max-w-[430px] sm:max-w-[620px] lg:max-w-6xl 2xl:max-w-7xl auth-card backdrop-blur-3xl rounded-[26px] lg:rounded-[34px] overflow-hidden">

            <div class="grid grid-cols-1 lg:grid-cols-[0.82fr_1.18fr]">

                <!-- Panel izquierdo para PC -->
                <div class="hidden lg:flex flex-col justify-between compact-desktop side-panel p-8 xl:p-10">

                    <div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full icon-box text-[10px] font-black uppercase tracking-[0.25em]">
                            <i class="fas fa-star"></i>
                            Observatorio Astral
                        </div>

                        <div class="mt-10 xl:mt-14">
                            <div class="relative w-20 h-20 xl:w-24 xl:h-24 rounded-[28px] icon-box flex items-center justify-center shadow-[0_0_55px_rgba(59,130,246,0.25)]">
                                <i class="fas fa-user-plus text-3xl xl:text-4xl"></i>

                                <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-full flex items-center justify-center border-4"
                                     style="background: var(--success); border-color: var(--surface);">
                                    <i class="fas fa-bolt text-white text-xs"></i>
                                </div>
                            </div>

                            <h1 class="mt-7 text-3xl xl:text-4xl font-black uppercase tracking-widest leading-tight">
                                Crear <br>
                                cuenta
                            </h1>

                            <p class="mt-4 text-sm muted leading-7 max-w-sm">
                                Accede al sistema de reservas y visitas con una experiencia moderna, clara y adaptable.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-8">
                        <div class="flex items-center gap-3 muted text-xs font-bold">
                            <div class="w-8 h-8 rounded-xl icon-box flex items-center justify-center">
                                <i class="fas fa-check text-[11px]"></i>
                            </div>
                            Registro simple y validado
                        </div>

                        <div class="flex items-center gap-3 muted text-xs font-bold">
                            <div class="w-8 h-8 rounded-xl icon-box flex items-center justify-center">
                                <i class="fas fa-envelope text-[11px]"></i>
                            </div>
                            Confirmación por correo electrónico
                        </div>

                        <div class="flex items-center gap-3 muted text-xs font-bold">
                            <div class="w-8 h-8 rounded-xl icon-box flex items-center justify-center">
                                <i class="fas fa-moon text-[11px]"></i>
                            </div>
                            Tema claro y oscuro adaptable
                        </div>
                    </div>
                </div>

                <!-- Panel formulario -->
                <div class="p-5 sm:p-7 lg:p-8 xl:p-10 compact-desktop">

                    <!-- Header móvil/tablet -->
                    <div class="lg:hidden text-center mb-6">
                        <div class="flex justify-center mb-4">
                            <div class="relative w-16 h-16 rounded-2xl icon-box flex items-center justify-center">
                                <i class="fas fa-user-plus text-2xl"></i>

                                <div class="absolute -bottom-2 -right-2 w-7 h-7 rounded-full flex items-center justify-center border-4"
                                     style="background: var(--success); border-color: var(--surface);">
                                    <i class="fas fa-bolt text-white text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <h2 class="font-black text-[24px] uppercase tracking-widest">
                            Regístrate
                        </h2>

                        <p class="primary-text text-[10px] font-bold uppercase tracking-[0.16em] mt-2 leading-relaxed">
                            Creación de cuenta y acceso inmediato
                        </p>
                    </div>

                    <!-- Header PC -->
                    <div class="hidden lg:block mb-6">
                        <h2 class="font-black text-2xl uppercase tracking-widest">
                            Registro de usuario
                        </h2>
                        <p class="muted text-xs mt-2">
                            Completa tus datos para crear tu cuenta.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <!-- Nombres -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Nombres
                                </label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           placeholder="Tu nombre"
                                           required
                                           autocomplete="name"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                                @error('name')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Apellidos
                                </label>
                                <div class="relative">
                                    <i class="fas fa-user-tag absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="text"
                                           name="apellido"
                                           value="{{ old('apellido') }}"
                                           placeholder="Tu apellido"
                                           required
                                           autocomplete="family-name"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                                @error('apellido')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- CI -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Cédula de Identidad
                                </label>
                                <div class="relative">
                                    <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="text"
                                           name="ci"
                                           value="{{ old('ci') }}"
                                           placeholder="Nº de documento"
                                           required
                                           inputmode="numeric"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                                @error('ci')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Celular -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Celular
                                </label>
                                <div class="relative">
                                    <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="tel"
                                           name="telefono"
                                           value="{{ old('telefono') }}"
                                           placeholder="Ej: 77712345"
                                           required
                                           inputmode="numeric"
                                           autocomplete="tel"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                                @error('telefono')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-black primary-text uppercase tracking-widest mb-2">
                                    Correo para notificación
                                </label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 primary-text text-xs"></i>
                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="ejemplo@correo.com"
                                           required
                                           autocomplete="email"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3.5 text-sm font-bold">
                                </div>
                                <p class="text-[9px] muted italic mt-1 ml-1">
                                    Recibirás un correo de confirmación al completar el registro.
                                </p>
                                @error('email')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Contraseña
                                </label>
                                <div class="relative">
                                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="password"
                                           name="password"
                                           required
                                           autocomplete="new-password"
                                           placeholder="••••••••"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                                @error('password')
                                    <p class="text-red-400 text-[10px] font-bold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirmar Password -->
                            <div>
                                <label class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                    Repetir contraseña
                                </label>
                                <div class="relative">
                                    <i class="fas fa-key absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>
                                    <input type="password"
                                           name="password_confirmation"
                                           required
                                           autocomplete="new-password"
                                           placeholder="••••••••"
                                           class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3 text-sm">
                                </div>
                            </div>

                        </div>

                        <!-- Botón -->
                        <div class="mt-6">
                            <button type="submit"
                                    class="group relative w-full overflow-hidden primary-button font-black py-4 rounded-2xl transition-all uppercase tracking-[0.18em] sm:tracking-[0.22em] text-[10px] sm:text-xs active:scale-[0.98] hover:scale-[1.01]">
                                <span class="relative z-10 flex items-center justify-center gap-3">
                                    Crear cuenta e ingresar
                                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                </span>
                                <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            </button>
                        </div>

                        <!-- Login -->
                        <div class="text-center mt-5">
                            <a href="{{ route('login') }}"
                               class="muted hover:opacity-100 text-[10px] font-bold uppercase tracking-widest transition-all">
                                ¿Ya tienes cuenta?
                                <span class="primary-text underline underline-offset-4 ml-1 text-xs">
                                    Entrar
                                </span>
                            </a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);

            const icon = document.getElementById('themeIcon');

            if (icon) {
                icon.className = theme === 'dark'
                    ? 'fas fa-sun text-sm accent-text'
                    : 'fas fa-moon text-sm primary-text';
            }
        }

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));
        });
    </script>
</x-guest-layout>