<x-guest-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-[#020617] flex items-center justify-center p-3 sm:p-6 relative overflow-x-hidden font-['Inter']">
        <!-- Fondo Cinematic Premium -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_18%_18%,rgba(56,189,248,0.12),transparent_40%),radial-gradient(circle_at_82%_84%,rgba(59,130,246,0.15),transparent_42%),linear-gradient(180deg,#020617_0%,#031126_100%)]"></div>

        <!-- Botón Volver -->
        <div class="absolute top-4 left-4 z-50">
            <a href="{{ route('bienvenido') }}" class="group flex items-center gap-2 text-white/50 hover:text-blue-400 transition-all duration-300">
                <div class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center bg-white/5 group-hover:border-blue-500/50 group-hover:bg-blue-500/10">
                    <i class="fas fa-arrow-left text-xs"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest hidden sm:block">Volver</span>
            </a>
        </div>

        <div class="relative w-full max-w-2xl bg-[#0f172a]/95 backdrop-blur-3xl rounded-[32px] border border-white/10 shadow-2xl overflow-hidden my-10">
            
            <!-- Cabecera de Confirmación Visual -->
            <div class="w-full bg-white/5 border-b border-white/10 p-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="relative">
                        <div class="w-20 h-20 rounded-full border-4 flex items-center justify-center bg-blue-600/20 border-blue-500 shadow-[0_0_40px_-5px_rgba(59,130,246,0.6)]">
                            <i class="fas fa-user-plus text-3xl text-blue-400"></i>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-green-500 rounded-full flex items-center justify-center border-4 border-[#0f172a]">
                            <i class="fas fa-bolt text-white text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <h2 class="text-white font-black text-xl tracking-widest uppercase">Registrate</h2>
                <p class="text-blue-400/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-2">Creación de cuenta y acceso inmediato</p>
            </div>

            <!-- Formulario de Registro e Inicio -->
            <div class="p-8 sm:p-12">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Nombres -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Nombres</label>
                            <input type="text" name="name" placeholder="Tu nombre" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-white/20">
                        </div>

                        <!-- Apellidos -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Apellidos</label>
                            <input type="text" name="apellido" placeholder="Tu apellido" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-white/20">
                        </div>

                        <!-- Cédula de Identidad -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Cédula de Identidad</label>
                            <input type="text" name="ci" placeholder="Nº de documento" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-white/20">
                        </div>

                        <!-- Teléfono -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Celular</label>
                            <input type="text" name="telefono" placeholder="Ej: 77712345" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-white/20">
                        </div>

                        <!-- Email - Resaltado porque será el de la notificación -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-[10px] font-black text-blue-400 uppercase tracking-widest ml-1">Correo para Notificación</label>
                            <input type="email" name="email" placeholder="ejemplo@correo.com" required class="w-full bg-blue-500/10 border-2 border-blue-500/50 rounded-xl p-4 text-blue-100 font-bold text-sm outline-none shadow-[0_0_20px_rgba(59,130,246,0.15)]">
                            <p class="text-[9px] text-white/30 italic ml-1">Recibirás un correo de confirmación al completar el registro.</p>
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Contraseña</label>
                            <input type="password" name="password" placeholder="" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>

                        <!-- Confirmar Password -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-white/40 uppercase tracking-widest ml-1">Repetir Contraseña</label>
                            <input type="password" name="password_confirmation" placeholder="" required class="w-full bg-white/5 border border-white/10 rounded-xl p-3.5 text-white text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Botón de Acción Final -->
                    <div class="mt-12">
                        <button type="submit" class="w-full bg-gradient-to-r from-blue-700 to-blue-500 hover:from-blue-600 hover:to-blue-400 text-white font-black py-5 rounded-2xl transition-all uppercase tracking-[0.3em] text-xs shadow-2xl shadow-blue-900/40 transform hover:scale-[1.02] active:scale-[0.98]">
                            Crear Cuenta e Ingresar
                        </button>
                        
                        <div class="text-center mt-8">
                            <a href="{{ route('login') }}" class="text-white/40 hover:text-blue-400 text-[10px] font-bold uppercase tracking-widest transition-all">
                                ¿Ya tienes cuenta? <span class="text-blue-500 underline underline-offset-4 ml-1 text-xs">Entrar</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>