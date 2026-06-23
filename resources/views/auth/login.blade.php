<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap"
        rel="stylesheet">
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
            --danger: #dc2626;
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
            --danger: #f87171;
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
            background: rgba(255, 255, 255, 0.74);
            border: 1px solid var(--border);
            box-shadow: 0 30px 100px var(--shadow);
            color: var(--text);
        }

        [data-theme="dark"] .auth-card {
            background: rgba(15, 23, 42, 0.92);
        }

        .side-panel {
            background:
                radial-gradient(circle at top left, var(--primary-soft), transparent 42%),
                radial-gradient(circle at bottom right, rgba(251, 191, 36, 0.12), transparent 40%),
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
            opacity: .72;
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

        .success-text {
            color: var(--success);
        }

        .danger-text {
            color: var(--danger);
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

        .status-box {
            background: rgba(22, 163, 74, 0.10);
            border: 1px solid rgba(22, 163, 74, 0.28);
            color: var(--success);
        }

        [data-theme="dark"] .status-box {
            background: rgba(34, 197, 94, 0.10);
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .error-box {
            background: rgba(220, 38, 38, 0.10);
            border: 1px solid rgba(220, 38, 38, 0.28);
            color: var(--danger);
        }

        [data-theme="dark"] .error-box {
            background: rgba(248, 113, 113, 0.10);
            border: 1px solid rgba(248, 113, 113, 0.25);
        }

        @media (max-height: 700px) and (min-width: 1024px) {
            .compact-desktop {
                padding-top: 1.5rem !important;
                padding-bottom: 1.5rem !important;
            }

            .compact-input {
                padding-top: .72rem !important;
                padding-bottom: .72rem !important;
            }
        }
    </style>

    <div
        class="min-h-[100dvh] auth-bg relative overflow-x-hidden flex items-center justify-center px-3 pt-16 pb-5 sm:px-6 sm:py-6 lg:px-8">

        <!-- Botón volver -->
        <div class="fixed top-4 left-4 z-50">
            <a href="{{ route('bienvenido') }}"
                class="group flex items-center gap-2 muted hover:opacity-100 opacity-80 transition-all duration-300">
                <div
                    class="w-10 h-10 rounded-full glass-button flex items-center justify-center group-hover:scale-105 transition">
                    <i class="fas fa-arrow-left text-xs"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest hidden sm:block">
                    Volver
                </span>
            </a>
        </div>

        <!-- Botón tema -->
        <button type="button" onclick="toggleTheme()"
            class="fixed top-4 right-4 z-50 w-10 h-10 rounded-full glass-button flex items-center justify-center hover:scale-105 transition"
            title="Cambiar tema">
            <i id="themeIcon" class="fas fa-moon text-sm primary-text"></i>
        </button>

        <!-- Decoración -->
        <div class="absolute top-24 left-8 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 right-8 w-52 h-52 bg-yellow-400/10 rounded-full blur-3xl"></div>

        <!-- Card principal -->
        <div
            class="relative z-10 w-full max-w-[430px] sm:max-w-[620px] lg:max-w-6xl 2xl:max-w-7xl auth-card backdrop-blur-3xl rounded-[26px] lg:rounded-[34px] overflow-hidden">

            <div class="grid grid-cols-1 lg:grid-cols-[0.92fr_1.08fr]">

                <!-- Panel izquierdo PC -->
                <div class="hidden lg:flex flex-col justify-between compact-desktop side-panel p-8 xl:p-10">

                    <div>
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full icon-box text-[10px] font-black uppercase tracking-[0.25em]">
                            <i class="fas fa-star"></i>
                            Observatorio Astral
                        </div>

                        <div class="mt-10 xl:mt-14">
                            <div
                                class="relative w-20 h-20 xl:w-24 xl:h-24 rounded-[28px] icon-box flex items-center justify-center shadow-[0_0_55px_rgba(59,130,246,0.25)]">
                                <i class="fas fa-meteor text-3xl xl:text-4xl"></i>

                                <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-full flex items-center justify-center border-4"
                                    style="background: var(--success); border-color: var(--surface);">
                                    <i class="fas fa-lock-open text-white text-xs"></i>
                                </div>
                            </div>

                            <h1 class="mt-7 text-3xl xl:text-4xl font-black uppercase tracking-widest leading-tight">
                                Iniciar <br>
                                sesión
                            </h1>

                            <p class="mt-4 text-sm muted leading-7 max-w-sm">
                                Accede al sistema de reservas y visitas del Observatorio Astronómico Max Schreier.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-8">
                        <div class="flex items-center gap-3 muted text-xs font-bold">
                            <div class="w-8 h-8 rounded-xl icon-box flex items-center justify-center">
                                <i class="fas fa-calendar-check text-[11px]"></i>
                            </div>
                            Gestión de reservas online
                        </div>

                        <div class="flex items-center gap-3 muted text-xs font-bold">
                            <div class="w-8 h-8 rounded-xl icon-box flex items-center justify-center">
                                <i class="fas fa-users text-[11px]"></i>
                            </div>
                            Control de visitas y usuarios
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
                <div class="p-5 sm:p-7 lg:p-8 xl:p-10 compact-desktop flex flex-col justify-center">

                    <!-- Header móvil/tablet -->
                    <div class="lg:hidden text-center mb-6">
                        <div class="flex justify-center mb-4">
                            <div class="relative w-16 h-16 rounded-2xl icon-box flex items-center justify-center">
                                <i class="fas fa-meteor text-2xl"></i>

                                <div class="absolute -bottom-2 -right-2 w-7 h-7 rounded-full flex items-center justify-center border-4"
                                    style="background: var(--success); border-color: var(--surface);">
                                    <i class="fas fa-lock-open text-white text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <h2 class="font-black text-[24px] uppercase tracking-widest">
                            Iniciar sesión
                        </h2>

                        <p class="primary-text text-[10px] font-bold uppercase tracking-[0.16em] mt-2 leading-relaxed">
                            Acceso al sistema de reservas
                        </p>
                    </div>

                    <!-- Header PC -->
                    <div class="hidden lg:block mb-7">
                        <h2 class="font-black text-2xl uppercase tracking-widest">
                            Acceso de usuario
                        </h2>
                        <p class="muted text-xs mt-2">
                            Ingresa tus credenciales para continuar.
                        </p>
                    </div>

                    <!-- Mensaje de registro exitoso -->
                    @if (session('status'))
                        <div class="status-box mb-5 rounded-2xl px-4 py-3 text-xs font-bold flex items-start gap-3">
                            <i class="fas fa-circle-check mt-0.5"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Errores -->
                    @if ($errors->any())
                        <div id="throttle-alert" class="error-box mb-5 rounded-2xl px-4 py-3 text-xs font-bold">
                            @foreach ($errors->all() as $error)
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-triangle-exclamation mt-0.5"></i>
                                    <p id="error-message" class="uppercase tracking-[0.08em] leading-relaxed">
                                        {{ $error }}
                                    </p>
                                </div>
                            @endforeach

                            <div id="recovery-suggestion" class="hidden mt-3 pt-3 border-t border-red-500/20 text-center">
                                <a href="{{ route('password.request') }}"
                                    class="text-[10px] muted hover:primary-text uppercase tracking-widest transition-all font-bold underline underline-offset-4">
                                    ¿Olvidaste tu contraseña? Restablecer acceso →
                                </a>
                            </div>
                        </div>

                        @if($errors->has('email') && str_contains($errors->first('email'), __('auth.throttle')))
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    let msgElement = document.getElementById('error-message');
                                    let suggestion = document.getElementById('recovery-suggestion');

                                    if (!msgElement) return;

                                    let match = msgElement.innerText.match(/\d+/);

                                    if (match) {
                                        let seconds = parseInt(match[0]);

                                        if (seconds >= 60 && suggestion) {
                                            suggestion.classList.remove('hidden');
                                        }

                                        let timer = setInterval(function () {
                                            seconds--;

                                            if (seconds <= 0) {
                                                clearInterval(timer);
                                                msgElement.innerText = "SISTEMA DESBLOQUEADO. INTENTA INICIAR SESIÓN NUEVAMENTE.";
                                                msgElement.classList.remove('danger-text');
                                                msgElement.classList.add('success-text');
                                            } else {
                                                msgElement.innerText = `DEMASIADOS INTENTOS. ACCESO RESTRINGIDO POR ${seconds} SEGUNDOS.`;
                                            }
                                        }, 1000);
                                    }
                                });
                            </script>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                Correo electrónico
                            </label>

                            <div class="relative">
                                <i
                                    class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 primary-text text-xs"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="username" placeholder="ejemplo@correo.com"
                                    class="compact-input input-box w-full rounded-2xl pl-10 pr-4 py-3.5 text-sm font-bold">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-[10px] font-black muted uppercase tracking-widest mb-2">
                                Contraseña
                            </label>

                            <div class="relative">
                                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 muted text-xs"></i>

                                <input id="password" type="password" name="password" required
                                    autocomplete="current-password" placeholder="••••••••"
                                    class="compact-input input-box w-full rounded-2xl pl-10 pr-12 py-3.5 text-sm">

                                <button type="button" onclick="togglePasswordVisibility('password', 'passwordIcon')"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 transition"
                                    style="color: var(--muted);" title="Mostrar u ocultar contraseña">
                                    <i id="passwordIcon" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Opciones -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="text-[11px] muted font-bold">
                                    Recordarme
                                </span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                    class="text-[11px] muted hover:primary-text transition font-bold underline underline-offset-4">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif

                        </div>

                        <!-- Botón -->
                        <button type="submit"
                            class="group relative w-full overflow-hidden primary-button font-black py-4 rounded-2xl transition-all uppercase tracking-[0.18em] sm:tracking-[0.22em] text-[10px] sm:text-xs active:scale-[0.98] hover:scale-[1.01]">
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                Iniciar sesión
                                <i
                                    class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </span>
                            <div
                                class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                            </div>
                        </button>

                        <!-- Registro -->
                        <div class="pt-5 border-t" style="border-color: var(--border);">
                            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-center">
                                <a href="{{ route('register') }}"
                                    class="muted hover:primary-text text-[10px] font-bold uppercase tracking-widest transition-all">
                                    ¿No tienes cuenta?
                                    <span class="primary-text underline underline-offset-4 ml-1 text-xs">
                                        Regístrate
                                    </span>
                                </a>

                                
                            </div>
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

    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (!input || !icon) return;

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        applyTheme(savedTheme || (prefersDark ? 'dark' : 'light'));
    });
</script>
</x-guest-layout>