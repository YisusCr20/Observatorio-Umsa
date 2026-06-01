<x-app-layout>
    <div class="p-6 bg-[#0f172a] min-h-screen text-slate-200 font-sans">

        <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Reportes de Reservas</h1>
        <p class="text-slate-400 text-sm mb-8">Análisis detallado de reservas y ocupación</p>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-[#1e293b] p-5 rounded-xl border border-slate-700/50 shadow-lg">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total</p>
                <h3 class="text-3xl font-bold text-white mt-2">{{ $estadisticas['total'] }}</h3>
            </div>
            <div class="bg-[#1e293b] p-5 rounded-xl border border-slate-700/50 shadow-lg">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Confirmadas</p>
                <h3 class="text-3xl font-bold text-green-400 mt-2">{{ $estadisticas['confirmadas'] }}</h3>
            </div>
            <div class="bg-[#1e293b] p-5 rounded-xl border border-slate-700/50 shadow-lg">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pendientes</p>
                <h3 class="text-3xl font-bold text-yellow-400 mt-2">{{ $estadisticas['pendientes'] }}</h3>
            </div>
            <div class="bg-[#1e293b] p-5 rounded-xl border border-slate-700/50 shadow-lg">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Canceladas</p>
                <h3 class="text-3xl font-bold text-red-400 mt-2">{{ $estadisticas['canceladas'] }}</h3>
            </div>
            <div class="bg-[#1e293b] p-5 rounded-xl border border-slate-700/50 shadow-lg">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Rechazadas</p>
                <h3 class="text-3xl font-bold text-orange-400 mt-2">{{ $estadisticas['rechazadas'] }}</h3>
            </div>
        </div>

        <!-- Ocupación por Turno -->
        <div class="bg-[#1e293b] p-6 rounded-xl border border-slate-700/50 shadow-lg">
            <h2 class="text-lg font-bold text-white mb-4">Ocupación por Turno</h2>
            <div class="space-y-4">
                @foreach($reservasPorTurno as $turno)
                    <div class="border-l-4 border-blue-600 bg-slate-800/30 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-white font-semibold">{{ $turno->nombre }}</h3>
                                <p class="text-slate-400 text-sm">{{ $turno->hora_inicio }} - {{ $turno->hora_fin }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-white font-bold">
                                    {{ $turno->reservas->sum('cantidad_personas') }} / {{ $turno->capacidad_maxima }}
                                    personas
                                </p>
                                <div class="w-32 h-2 bg-slate-700 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-blue-600"
                                        style="width: {{ $turno->capacidad_maxima > 0 ? min(100, round(($turno->reservas->sum('cantidad_personas') / $turno->capacidad_maxima) * 100)) : 0 }}%">
                                    </div>
                                </div>
                                <p class="text-slate-400 text-xs mt-1">
                                    {{ $turno->capacidad_maxima > 0 ? round(($turno->reservas->sum('cantidad_personas') / $turno->capacidad_maxima) * 100) : 0 }}%
                                    ocupado</p>
                            </div>
                        </div>
                        <div class="flex gap-4 mt-3 pt-3 border-t border-slate-700/50 text-xs text-slate-400">
                            <span>📋 {{ $turno->reservas->count() }} reservas</span>
                            <span>✓ {{ $turno->reservas->where('estado', 'Confirmado')->count() }} confirmadas</span>
                            <span>⏳ {{ $turno->reservas->where('estado', 'Pendiente')->count() }} pendientes</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>