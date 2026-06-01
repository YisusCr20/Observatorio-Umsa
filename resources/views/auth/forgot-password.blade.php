<x-guest-layout>
    <!-- Tipografías de Prestigio -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Plus+Jakarta+Sans:wght@200;400;600;800&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">

    <div class="min-h-screen w-full flex items-center justify-center bg-[#020617] p-4 md:p-8 font-['Plus_Jakarta_Sans'] relative overflow-hidden">

        <!-- FONDO DINÁMICO (Mismo que el Login) -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-cover bg-center scale-110 animate-slow-zoom"
                style="background-image: url('https://images.unsplash.com/photo-1462331940025-496dfbfc7564?q=80&w=2022&auto=format&fit=crop'); filter: brightness(0.3) contrast(1.1);">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/20 via-transparent to-purple-900/20"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,#020617_100%)]"></div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="relative z-10 w-full max-w-[1200px] flex flex-col lg:flex-row bg-white/[0.01] backdrop-blur-[50px] border border-white/10 rounded-[30px] md:rounded-[60px] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.7)]">

            <!-- SECCIÓN IZQUIERDA: FORMULARIO DE RESCATE -->
            <div class="w-full lg:w-[45%] p-8 sm:p-12 md:p-20 flex flex-col justify-center relative bg-black/20">

                <!-- Logo/Marca Adaptada -->
                <div class="mb-10 group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-[1px] w-8 bg-blue-500 group-hover:w-16 transition-all duration-700"></div>
                        <span class="font-['Syncopate'] text-[9px] tracking-[0.5em] text-blue-400 font-bold uppercase">Seguridad</span>
                    </div>
                    <h1 class="font-['Cinzel'] text-2xl md:text-4xl text-white font-black leading-none tracking-tighter">
                        RECUPERA<br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-white">TU CONTRASEÑA</span>
                    </h1>
                </div>

                <!-- Mensaje de Instrucción Estilizado -->
                <div class="mb-8 text-white/50 text-xs leading-relaxed italic tracking-wider">
                    {{ __('¿Olvidaste tu contraseña? No hay problema. Ingresa tu correo electrónico institucional y te enviaremos un enlace de rescate para restablecer tu acceso.') }}
                </div>

                <!-- Session Status (Feedback de envío exitoso) -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs font-bold animate-pulse text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-8">
                    @csrf

                    <!-- Campo Email con tu estilo de inputs -->
                    <div class="space-y-2">
                        <label class="block text-[10px] uppercase tracking-[0.3em] text-white/40 ml-1 font-bold italic">Correo Electronico</label>
                        <div class="relative group">
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder=""
                                class="w-full bg-white/[0.03] border-b border-white/10 text-white text-sm px-4 py-4 focus:border-blue-400 transition-all outline-none placeholder:text-white/10 group-hover:bg-white/[0.05]">
                            <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-blue-400 group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-[10px] text-red-400 uppercase tracking-widest font-bold" />
                    </div>

                    <!-- Botón Principal -->
                    <button class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black text-[10px] uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-blue-900/40 transition-all duration-500 transform hover:-translate-y-1">
                        {{ __('Enviar') }}
                    </button>

                    <!-- Footer de Navegación -->
                    <div class="mt-12 pt-8 border-t border-white/5 flex justify-center">
                        <a href="{{ route('login') }}"
                            class="text-[10px] px-8 py-2 rounded-full border border-white/10 text-white/40 hover:border-blue-500 hover:text-white transition-all uppercase tracking-[0.2em] font-bold">
                            Volver
                        </a>
                    </div>
                </form>
            </div>

            <!-- SECCIÓN DERECHA: ICONOGRAFÍA DE SEGURIDAD (Visible en Desktop) -->
            <div class="hidden lg:flex w-[55%] relative items-center justify-center p-20 overflow-hidden bg-[#050a15]">
                <img src="https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=2072"
                    class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-screen">

                <div class="relative z-10 text-center">
                    <div class="inline-block p-[2px] bg-gradient-to-tr from-blue-500 via-blue-300 to-transparent rounded-full mb-8">
                        <div class="bg-[#050a15] rounded-full p-6 backdrop-blur-xl">
                            <!-- Icono de Llave Estelar -->
                            <svg class="w-12 h-12 text-blue-400 shadow-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-['Cinzel'] text-3xl text-white font-black mb-4 tracking-[0.2em] uppercase">
                        Protocolo de <br> <span class="italic text-blue-400">Acceso Seguro</span>
                    </h3>
                    <p class="text-white/30 text-[9px] tracking-[0.5em] uppercase leading-loose max-w-xs mx-auto font-bold">
                        El sistema validará su identidad mediante el envío de un token criptográfico a su bandeja institucional.
                    </p>
                </div>

                <!-- Adornos Tecnológicos -->
                <div class="absolute top-[-100px] left-[-100px] w-80 h-80 border border-blue-500/10 rounded-full animate-pulse"></div>
                <div class="absolute bottom-[-100px] right-[-100px] w-96 h-96 border border-blue-400/5 rounded-full"></div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slow-zoom {
            0% { transform: scale(1); }
            50% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }
        .animate-slow-zoom { animation: slow-zoom 30s ease-in-out infinite; }
        .shadow-glow { filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.6)); }
    </style>
</x-guest-layout>