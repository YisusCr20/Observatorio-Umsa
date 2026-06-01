<x-guest-layout>
    <!-- Tipografías de Prestigio -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Plus+Jakarta+Sans:wght@200;400;600;800&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">

    <div class="min-h-screen w-full flex items-center justify-center bg-[#020617] p-4 md:p-8 font-['Plus_Jakarta_Sans'] relative overflow-hidden">

        <!-- FONDO DINÁMICO RESPONSIVO -->
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-cover bg-center scale-110 animate-slow-zoom"
                style="background-image: url('https://images.unsplash.com/photo-1462331940025-496dfbfc7564?q=80&w=2022&auto=format&fit=crop'); filter: brightness(0.3) contrast(1.1);">
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/20 via-transparent to-purple-900/20"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,#020617_100%)]"></div>
        </div>

        <!-- CONTENEDOR PRINCIPAL -->
        <div class="relative z-10 w-full max-w-[1200px] flex flex-col lg:flex-row bg-white/[0.01] backdrop-blur-[50px] border border-white/10 rounded-[30px] md:rounded-[60px] overflow-hidden shadow-[0_50px_100px_-20px_rgba(0,0,0,0.7)]">

            <!-- SECCIÓN IZQUIERDA: FORMULARIO -->
            <div class="w-full lg:w-[45%] p-8 sm:p-12 md:p-20 flex flex-col justify-center relative bg-black/20">

                <!-- Logo/Marca -->
                <div class="mb-12 group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="h-[1px] w-8 bg-blue-500 group-hover:w-16 transition-all duration-700"></div>
                        <span class="font-['Syncopate'] text-[10px] tracking-[0.5em] text-blue-400 font-bold uppercase">Observatorio</span>
                    </div>
                    <h1 class="font-['Cinzel'] text-3xl md:text-5xl text-white font-black leading-none tracking-tighter">
                        MAX <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-white">SCHREIER</span>
                    </h1>
                </div>

                <!-- ALERTA DE BLOQUEO CON CUENTA REGRESIVA -->
                @if ($errors->any())
                    <div id="throttle-alert" class="mb-8 p-4 rounded-xl {{ $errors->has('email') && str_contains($errors->first('email'), __('auth.throttle')) ? 'bg-red-500/10 border-red-500/30 animate-pulse' : 'bg-blue-500/5 border-blue-500/20' }} border backdrop-blur-md">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 {{ str_contains($error, __('auth.throttle')) ? 'text-red-400' : 'text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p id="error-message" class="text-[10px] {{ str_contains($error, __('auth.throttle')) ? 'text-red-400' : 'text-blue-300' }} uppercase tracking-[0.1em] font-bold">
                                    {{ $error }}
                                </p>
                            </div>
                        @endforeach

                        <!-- Sugerencia de Recuperación si el tiempo es largo -->
                        <div id="recovery-suggestion" class="hidden mt-3 pt-3 border-t border-red-500/20 text-center">
                            <a href="{{ route('password.request') }}" class="text-[9px] text-white/70 hover:text-white uppercase tracking-widest transition-all font-bold italic underline underline-offset-4 decoration-red-500/50">
                                ¿Protocolo de seguridad activo? Restablezca su clave →
                            </a>
                        </div>
                    </div>

                    @if($errors->has('email') && str_contains($errors->first('email'), __('auth.throttle')))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            let msgElement = document.getElementById('error-message');
                            let suggestion = document.getElementById('recovery-suggestion');
                            
                            // Extraemos el número de segundos del mensaje de Laravel
                            let match = msgElement.innerText.match(/\d+/);
                            if (match) {
                                let seconds = parseInt(match[0]);
                                
                                // Si el bloqueo es de 2 minutos (120s), mostramos sugerencia de olvido
                                if (seconds >= 60) {
                                    suggestion.classList.remove('hidden');
                                }

                                let timer = setInterval(function() {
                                    seconds--;
                                    if (seconds <= 0) {
                                        clearInterval(timer);
                                        msgElement.innerText = "SISTEMA DESBLOQUEADO. REINTENTE AHORA.";
                                        msgElement.classList.replace('text-red-400', 'text-green-400');
                                        document.getElementById('throttle-alert').classList.remove('animate-pulse');
                                    } else {
                                        msgElement.innerText = `DEMASIADOS INTENTOS. ACCESO RESTRINGIDO POR ${seconds} SEGUNDOS.`;
                                    }
                                }, 1000);
                            }
                        });
                    </script>
                    @endif
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6 md:space-y-8">
                    @csrf

                    <!-- Campo Email -->
                    <div class="space-y-2">
                        <label class="block text-[10px] uppercase tracking-[0.3em] text-white/40 ml-1 font-bold italic">Correo Electrónico</label>
                        <div class="relative group">
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="usuario@umsa.bo"
                                class="w-full bg-white/[0.03] border-b border-white/10 text-white text-sm px-4 py-4 focus:border-blue-500 transition-all outline-none placeholder:text-white/10 group-hover:bg-white/[0.05]">
                            <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>

                    <!-- Campo Password -->
                    <div class="space-y-2">
                        <label class="block text-[10px] uppercase tracking-[0.3em] text-white/40 ml-1 font-bold italic">Contraseña</label>
                        <div class="relative group">
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full bg-white/[0.03] border-b border-white/10 text-white text-sm px-4 py-4 focus:border-blue-500 transition-all outline-none group-hover:bg-white/[0.05]">
                            <div class="absolute bottom-0 left-0 h-[2px] w-0 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2 mb-4">
                        <a class="text-[11px] text-gray-500 hover:text-blue-400 transition-colors duration-300 underline underline-offset-8 decoration-white/10"
                            href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs uppercase tracking-[0.2em] rounded-xl shadow-lg shadow-blue-900/40 transition-all duration-500 transform hover:-translate-y-1">
                        INICIAR SESIÓN
                    </button>

                    <!-- Footer del Formulario -->
                    <div class="mt-12 pt-8 border-t border-white/5 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="{{ route('register') }}"
                            class="text-[10px] text-white/30 hover:text-blue-400 uppercase tracking-widest transition-colors font-bold">Solicitar Registro</a>
                        <a href="{{ route('bienvenido') }}"
                            class="text-[10px] px-6 py-2 rounded-full border border-white/10 text-white/50 hover:border-blue-500 hover:text-white transition-all uppercase tracking-tighter">Volver</a>
                    </div>
                </form>
            </div>

            <!-- SECCIÓN DERECHA: IMPACTO -->
            <div class="hidden lg:flex w-[55%] relative items-center justify-center p-20 overflow-hidden bg-[#050a15]">
                <img src="https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=2072"
                    class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-luminosity">

                <div class="relative z-10 text-center">
                    <div class="inline-block p-1 bg-gradient-to-tr from-blue-500 to-cyan-300 rounded-full mb-8 animate-pulse">
                        <div class="bg-[#050a15] rounded-full p-4">
                            <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M11 3a1 1 0 10-2 0 1 1 0 002 0zM12 5V3m0 18v-2M5 12H3m18 0h-2m-2.03-7.07l1.41-1.41M5.64 18.36l1.41-1.41m12.72 0l-1.41 1.41M5.64 5.64l1.41 1.41" />
                            </svg>
                        </div>
                    </div>
                    <h3 class="font-['Cinzel'] text-4xl text-white font-black mb-6 tracking-widest uppercase">
                        SISTEMA DE <br> <span class="italic text-blue-400">RESERVAS ONLINE</span>
                    </h3>
                    <p class="text-white/40 text-xs tracking-[0.4em] uppercase leading-loose max-w-sm mx-auto">
                        Gestión automatizada de visitas y experiencias astronómicas UMSA.
                    </p>
                </div>

                <div class="absolute bottom-[-50px] right-[-50px] w-64 h-64 border border-blue-500/20 rounded-full"></div>
                <div class="absolute bottom-[-80px] right-[-80px] w-96 h-96 border border-blue-500/10 rounded-full"></div>
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
    </style>
</x-guest-layout>