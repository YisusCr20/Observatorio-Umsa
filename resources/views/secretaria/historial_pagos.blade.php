<x-app-layout>
    <script src="https://unpkg.com/lucide@latest"></script>

    <div class="min-h-screen bg-[#F4F7FE] text-slate-900 font-sans">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.35em] text-blue-500">Secretaría</p>
                    <h1 class="text-3xl font-black text-slate-900">Historial de pagos</h1>
                    <p class="text-sm font-semibold text-slate-500 mt-1">Registra pagos, confirma reservas y descarga reportes.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('secretaria.dashboard') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-3 text-xs font-black uppercase tracking-widest text-slate-700 shadow-sm border border-slate-200">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('secretaria.pagos.pdf') }}"
                       class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-xs font-black uppercase tracking-widest text-white shadow-lg shadow-emerald-600/20">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Reporte pagos
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-bold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
                <section class="xl:col-span-5 bg-white rounded-[28px] border border-slate-200 shadow-sm p-5">
                    <h2 class="text-sm font-black uppercase tracking-widest border-b border-slate-100 pb-3 mb-5">
                        Registrar pago
                    </h2>

                    <form method="POST" action="{{ route('secretaria.pagos.manual.store') }}" class="space-y-4">
                        @csrf

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Reserva pendiente de pago</span>
                            <select name="reserva_id" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm">
                                <option value="">Selecciona una reserva</option>
                                @foreach($reservasSinPago as $reserva)
                                    <option value="{{ $reserva->id }}">
                                        {{ $reserva->fecha?->format('d/m/Y') }} -
                                        {{ $reserva->horario->hora_inicio ?? 'Sin hora' }} -
                                        {{ $reserva->nombre ?? optional($reserva->user)->name }} -
                                        {{ $reserva->cantidad_personas }} pax
                                    </option>
                                @endforeach
                            </select>
                            @error('reserva_id') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Monto Bs.</span>
                                <input type="number" step="0.01" min="0" name="monto" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="0.00">
                                @error('monto') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Método</span>
                                <select name="metodo_pago" required class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm">
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="QR">QR</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Depósito">Depósito</option>
                                </select>
                            </label>
                        </div>

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">Nro. comprobante</span>
                            <input type="text" name="nro_comprobante" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="Ej. QR-2026-001">
                        </label>

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">OBSERVACIÓN</span>
                            <textarea name="observacion" rows="3" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm" placeholder="Detalle interno del pago o validación."></textarea>
                        </label>

                        <button class="w-full rounded-2xl bg-blue-600 hover:bg-blue-700 text-white py-3 text-xs font-black uppercase tracking-widest shadow-lg shadow-blue-600/20">
                            Guardar pago y confirmar reserva
                        </button>
                    </form>
                </section>

                <section class="xl:col-span-7 bg-white rounded-[28px] border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-black uppercase tracking-widest">Pagos registrados</h2>
                            <p class="text-xs font-semibold text-slate-500 mt-1">Historial actualizado por secretaría.</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-500">
                                <tr>
                                    <th class="p-4">Fecha</th>
                                    <th class="p-4">Visitante</th>
                                    <th class="p-4">Método</th>
                                    <th class="p-4">Monto</th>
                                    <th class="p-4">Comprobante</th>
                                    <th class="p-4">Registró</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($pagos as $pago)
                                    <tr class="hover:bg-blue-50/40">
                                        <td class="p-4 font-bold text-slate-600">{{ optional($pago->pagado_en ?? $pago->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="p-4">
                                            <p class="font-black text-slate-800">{{ $pago->reserva->nombre ?? optional($pago->reserva->user)->name ?? 'Visitante' }}</p>
                                            <p class="text-xs text-slate-400">{{ $pago->reserva->correo ?? optional($pago->reserva->user)->email }}</p>
                                        </td>
                                        <td class="p-4 font-bold">{{ $pago->metodo_pago }}</td>
                                        <td class="p-4 font-black text-emerald-600">Bs. {{ number_format((float) $pago->monto, 2) }}</td>
                                        <td class="p-4 text-slate-500">{{ $pago->nro_comprobante ?: 'N/A' }}</td>
                                        <td class="p-4 text-slate-500">{{ optional($pago->secretaria)->name ?? 'Sistema' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-10 text-center text-slate-400 font-bold">Todavía no hay pagos registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 border-t border-slate-100">
                        {{ $pagos->links() }}
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</x-app-layout>
