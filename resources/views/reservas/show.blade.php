<x-app-layout>
    {{-- Scripts para que todo funcione igual que en el Edit --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <div x-data="{ 
            darkMode: localStorage.getItem('theme') === 'dark',
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                this.$nextTick(() => lucide.createIcons());
            }
        }" 
        x-init="this.$nextTick(() => lucide.createIcons())"
        :class="darkMode ? 'dark bg-[#0F1012] text-white' : 'bg-[#F4F7FE] text-slate-900'"
        class="min-h-screen transition-colors duration-500 font-sans antialiased pb-12" x-cloak>

        {{-- HEADER: COPIA EXACTA DEL EDIT --}}
        <header class="sticky top-0 z-[150] p-4 lg:px-8 border-b transition-colors duration-500 backdrop-blur-md"
            :class="darkMode ? 'bg-[#16181A]/80 border-[#2A2D30]' : 'bg-white/80 border-gray-200'">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    {{-- Botón Volver Cuadrado --}}
                    <a href="{{ route('reservas.index') }}" 
                        :class="darkMode ? 'bg-[#212427] text-white hover:bg-red-500' : 'bg-white text-slate-600 hover:bg-red-500 hover:text-white border-gray-100'"
                        class="w-10 h-10 rounded-xl shadow-sm flex items-center justify-center transition-all group border border-transparent">
                        <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                    </a>
                    <div>
                        <h1 class="text-lg font-black uppercase tracking-tight">Detalles de Visita</h1>
                        <p class="text-[10px] font-bold text-blue-500">RES-{{ $reserva->id }}</p>
                    </div>
                </div>

                {{-- Botón Tema Cuadrado --}}
                <button @click="toggleTheme()" 
                    :class="darkMode ? 'bg-[#212427] text-yellow-400' : 'bg-white text-orange-500 border-gray-100'"
                    class="w-10 h-10 flex items-center justify-center rounded-xl shadow-sm border border-transparent transition-all active:scale-90">
                    <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <div class="max-w-5xl mx-auto mt-12 px-4">
            {{-- CONTENEDOR PRINCIPAL: MISMO ESTILO QUE EL EDIT --}}
            <div :class="darkMode ? 'bg-[#16181A] border-[#2A2D30]' : 'bg-white border-gray-100'"
                class="rounded-[2.5rem] shadow-2xl border transition-colors duration-500 overflow-hidden">
                
                <div class="p-8 md:p-12">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        
                        {{-- COLUMNA IZQUIERDA --}}
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Franja Horaria</label>
                                <div :class="darkMode ? 'bg-[#212427] text-white' : 'bg-gray-100 text-slate-800'"
                                    class="py-4 px-6 rounded-2xl font-black text-xs uppercase shadow-inner">
                                    {{ $reserva->turno->nombre }}
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Día y Horario</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div :class="darkMode ? 'bg-[#212427]' : 'bg-blue-50 border-blue-100'" class="p-4 rounded-2xl text-center border border-transparent">
                                        <p class="text-[9px] font-bold text-blue-500 uppercase">Fecha</p>
                                        <p class="text-sm font-black">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }}</p>
                                    </div>
                                    <div :class="darkMode ? 'bg-[#212427]' : 'bg-blue-50 border-blue-100'" class="p-4 rounded-2xl text-center border border-transparent">
                                        <p class="text-[9px] font-bold text-blue-500 uppercase">Hora</p>
                                        <p class="text-sm font-black">
                                            {{ $reserva->horario ? \Carbon\Carbon::parse($reserva->horario->hora_inicio)->format('H:i') . ' - ' . \Carbon\Carbon::parse($reserva->horario->hora_fin)->format('H:i') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Estado</label>
                                <div class="flex items-center gap-3 p-4 rounded-2xl border" 
                                    :class="darkMode ? 'bg-[#1b1d20] border-[#2A2D30]' : 'bg-white border-gray-100 text-slate-700'">
                                    <div class="w-3 h-3 rounded-full animate-pulse {{ in_array($reserva->estado, ['Confirmado', 'Confirmada']) ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>
                                    <span class="text-xs font-black uppercase tracking-widest">{{ $reserva->estado }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- COLUMNA DERECHA --}}
                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Visitantes y Titular</label>
                                <div :class="darkMode ? 'bg-[#212427]' : 'bg-gray-100'" class="p-6 rounded-2xl space-y-4 shadow-inner">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Cantidad:</span>
                                        <span class="text-lg font-black text-blue-500">{{ $reserva->cantidad_personas }} Personas</span>
                                    </div>
                                    <div class="h-[1px] bg-gray-500/20"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Titular:</span>
                                        <span class="text-xs font-black uppercase">{{ $reserva->user->name }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Botones de Acción --}}
                            <div class="space-y-4 pt-4">
                                <a href="{{ route('reservas.edit', $reserva) }}"
                                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-black uppercase text-xs tracking-[0.3em] py-5 rounded-2xl shadow-xl shadow-blue-500/30 transition-all text-center transform active:scale-95">
                                    MODIFICAR RESERVA
                                </a>

                                <form method="POST" action="{{ route('reservas.destroy', $reserva) }}" onsubmit="return confirm('¿Seguro de cancelar?')">
                                    @csrf @method('DELETE')
                                    <button class="w-full bg-transparent border-2 border-red-500/20 hover:border-red-500 hover:bg-red-500 text-red-500 hover:text-white font-black uppercase text-xs tracking-[0.3em] py-5 rounded-2xl transition-all transform active:scale-95">
                                        CANCELAR VISITA
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div class="mt-12 pt-8 border-t" :class="darkMode ? 'border-[#2A2D30]' : 'border-gray-100'">
                        <label class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] block mb-4 text-center">Notas Adicionales</label>
                        <div :class="darkMode ? 'bg-[#212427] text-gray-400' : 'bg-gray-50 text-slate-600 border-gray-100'" 
                            class="p-6 rounded-2xl text-xs italic border border-transparent shadow-sm">
                            "{{ $reserva->descripcion ?? 'No hay observaciones registradas.' }}"
                        </div>
                    </div>

                </div>
            </div>
            <p class="mt-8 text-center text-[9px] font-bold text-gray-500 uppercase tracking-[0.4em]">Observatorio Astronómico Max Schreier</p>
        </div>
    </div>
</x-app-layout>