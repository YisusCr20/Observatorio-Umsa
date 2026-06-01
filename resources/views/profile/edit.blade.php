<x-app-layout>
    <div class="min-h-screen bg-[#f0f2f5] pb-12">
        <!-- PORTADA ESTILO FACEBOOK/RRSS -->
        <div class="relative h-60 md:h-80 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-800 shadow-lg">
            <div class="absolute -bottom-16 left-1/2 -translate-x-1/2 md:left-24 md:translate-x-0">
                <div class="relative">
                    <!-- Avatar Circular Grande -->
                    <div class="w-32 h-32 md:w-44 md:h-44 rounded-full bg-white p-1.5 shadow-xl">
                        <div class="w-full h-full rounded-full bg-blue-600 flex items-center justify-center text-white text-5xl font-black border-4 border-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <!-- Icono de Cámara (Decorativo) -->
                    <button class="absolute bottom-2 right-2 p-2 bg-slate-100 rounded-full border border-slate-300 hover:bg-slate-200 transition-colors shadow-sm">
                        📸
                    </button>
                </div>
            </div>
            
            <!-- Botón Volver Flotante -->
            <div class="absolute top-6 left-6">
                <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 px-4 py-2 bg-black/20 backdrop-blur-md text-white rounded-xl font-bold text-sm hover:bg-black/40 transition-all border border-white/20">
                    ← Volver
                </a>
            </div>
        </div>

        <!-- INFO PRINCIPAL DEBAJO DE LA PORTADA -->
        <div class="max-w-6xl mx-auto px-6 pt-20 md:pt-4 md:pl-72 flex flex-col md:flex-row justify-between items-center md:items-end gap-4">
            <div class="text-center md:text-left">
                <h1 class="text-3xl md:text-4xl font-black text-slate-900">{{ Auth::user()->name }}</h1>
                <p class="text-slate-500 font-bold uppercase tracking-widest text-xs mt-1">Estudiante del Observatorio Max Schreier</p>
            </div>
            <div class="flex gap-2">
                <button onclick="document.getElementById('edit-section').scrollIntoView({behavior: 'smooth'})" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
                    Editar Información
                </button>
            </div>
        </div>

        <!-- CONTENIDO EN TARJETAS -->
        <div class="max-w-6xl mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Columna de Datos (Izquierda) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-slate-100">
                    <h3 class="font-black text-slate-800 text-lg mb-6 flex items-center gap-2">
                        <span>ℹ️</span> Información
                    </h3>
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-slate-50 rounded-lg text-xl">📧</div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Correo Institucional</p>
                                <p class="text-sm font-bold text-slate-700">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-slate-50 rounded-lg text-xl">📅</div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Miembro desde</p>
                                <p class="text-sm font-bold text-slate-700">{{ Auth::user()->created_at->format('M Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="p-2 bg-slate-50 rounded-lg text-xl">🎓</div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Rol de Cuenta</p>
                                <p class="text-sm font-bold text-blue-600">{{ Auth::user()->role ?? 'Estudiante' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna de Formularios (Derecha) -->
            <div id="edit-section" class="lg:col-span-8 space-y-8">
                <!-- Card de Edición con el mismo diseño del Dashboard -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                        <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest">Actualizar mis datos de registro</h4>
                    </div>
                    <div class="p-8">
                        <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
                            @csrf
                            @method('patch')
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-700 ml-2">Nombre completo</label>
                                    <input type="text" name="name" value="{{ $user->name }}" class="w-full px-5 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-medium text-slate-700">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-slate-700 ml-2">Correo electrónico</label>
                                    <input type="email" name="email" value="{{ $user->email }}" class="w-full px-5 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 font-medium text-slate-700">
                                </div>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex justify-end">
                                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-blue-100">
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                        <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest">Cambiar Contraseña</h4>
                        <span class="text-[10px] font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded-lg italic">Recomendado cada 3 meses</span>
                    </div>
                    <div class="p-8">
                         <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                            @csrf
                            @method('put')
                            <input type="password" name="current_password" placeholder="Contraseña Actual" class="w-full px-5 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="password" name="password" placeholder="Nueva Contraseña" class="w-full px-5 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 text-sm">
                                <input type="password" name="password_confirmation" placeholder="Repetir Nueva Contraseña" class="w-full px-5 py-3 bg-slate-100 border-none rounded-2xl focus:ring-2 focus:ring-blue-600 text-sm">
                            </div>
                            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-black transition-all">
                                Actualizar Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>