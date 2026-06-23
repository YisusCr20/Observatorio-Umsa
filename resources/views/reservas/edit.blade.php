<x-app-layout>
    @php
        $embedded = request()->boolean('embedded');
        $fechaActual = $reserva->fecha instanceof \Carbon\Carbon ? $reserva->fecha->format('Y-m-d') : \Carbon\Carbon::parse($reserva->fecha)->format('Y-m-d');
        $horaActual = $reserva->horario
            ? substr($reserva->horario->hora_inicio, 0, 5) . ' - ' . substr($reserva->horario->hora_fin, 0, 5)
            : '08:30 - 10:00';
        $horarios = ['08:30 - 10:00', '10:00 - 11:30', '11:30 - 13:00', '13:00 - 14:30', '14:30 - 16:00', '16:00 - 17:30', '17:30 - 19:00'];
    @endphp

    @unless($embedded)
        <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script>
    @endunless
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        [x-cloak] { display: none !important; }
        @if($embedded)
            html, body { background: #F0F2F5 !important; overflow-x: hidden !important; }
            html.dark, html.dark body { background: #18191A !important; }
            body > .relative.min-h-screen { min-height: auto !important; background: #F0F2F5 !important; }
            html.dark body > .relative.min-h-screen { background: #18191A !important; }
            body > .relative.min-h-screen > .fixed { display: none !important; }
            body > .relative.min-h-screen > .relative.z-10.flex.min-h-screen { display: block !important; min-height: auto !important; }
            body > .relative.min-h-screen > .relative.z-10.flex.min-h-screen > main { width: 100% !important; overflow: visible !important; }
        @endif
    </style>

    <div x-data="editarReserva()" x-init="init()"
        :class="darkMode ? 'dark bg-[#18191A] text-gray-100' : 'bg-[#F0F2F5] text-gray-900'"
        class="{{ $embedded ? 'min-h-0 p-3 sm:p-4' : 'min-h-screen p-4 sm:p-6' }} font-sans antialiased transition-colors" x-cloak>

        <div class="max-w-4xl mx-auto space-y-4">
            <div class="bg-white dark:bg-[#242526] rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[#1877F2]">RES-{{ str_pad((string) $reserva->id, 4, '0', STR_PAD_LEFT) }}</p>
                        <h1 class="text-2xl font-black mt-2">Modificar reserva</h1>
                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 mt-1">
                            Este formulario solo ajusta datos puntuales. La reserva volverá a pendiente para revisión de secretaría.
                        </p>
                    </div>
                    <div class="rounded-2xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 px-4 py-3 text-amber-700 dark:text-amber-300">
                        <p class="text-[10px] font-black uppercase tracking-widest">Ventana de edición</p>
                        <p class="text-xs font-bold mt-1">Disponible solo dentro de las 24 horas previas a la visita.</p>
                    </div>
                </div>
            </div>

            <div x-show="message" x-transition
                class="rounded-2xl border px-4 py-3 text-sm font-bold flex items-start gap-2"
                :class="messageType === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-red-50 border-red-200 text-red-700'">
                <i :data-lucide="messageType === 'success' ? 'check-circle-2' : 'alert-circle'" class="w-5 h-5 shrink-0"></i>
                <span x-text="message"></span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-[1fr_0.85fr] gap-4">
                <form @submit.prevent="submit" class="bg-white dark:bg-[#242526] rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sm:p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Fecha ajustada</span>
                            <input type="date" x-model="form.fecha" :min="today" @change="validateDate" required
                                class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] px-4 py-3 text-sm font-black dark:text-white">
                            <p class="text-[10px] font-bold text-gray-400 mt-2">Atención de lunes a viernes. Sábados y domingos no están disponibles.</p>
                        </label>

                        <label class="block">
                            <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Horario ajustado</span>
                            <select x-model="form.hora_inicio" required
                                class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] px-4 py-3 text-sm font-black dark:text-white">
                                <template x-for="horario in horarios" :key="horario">
                                    <option :value="horario"
                                        :disabled="isSlotOccupied(horario)"
                                        x-text="isSlotOccupied(horario) ? horario + ' - Ocupado' : horario + ' - Libre'">
                                    </option>
                                </template>
                            </select>
                            <p class="text-[10px] font-bold text-gray-400 mt-2">Las horas ocupadas se bloquean automáticamente al cambiar la fecha.</p>
                        </label>
                    </div>

                    <label class="block">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Cantidad de asistentes actualizada</span>
                        <input type="number" min="1" x-model.number="form.cantidad_personas" required
                            class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] px-4 py-3 text-sm font-black dark:text-white">
                    </label>

                    <label class="block">
                        <span class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-2">Motivo / observación del cambio</span>
                        <textarea x-model="form.descripcion" rows="5" required minlength="10"
                            placeholder="Ej.: Un estudiante se enfermó, hubo bloqueo, cambió la cantidad de asistentes o se requiere ajustar la hora."
                            class="w-full rounded-2xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#18191A] px-4 py-3 text-sm font-bold dark:text-white"></textarea>
                        <p class="text-[10px] font-bold text-gray-400 mt-2">Secretaría recibirá esta observación en su panel y por correo.</p>
                    </label>

                    <div class="flex flex-col-reverse sm:flex-row sm:justify-between gap-3 pt-2">
                        <button type="button" @click="volver"
                            class="rounded-2xl bg-gray-100 dark:bg-[#18191A] text-gray-700 dark:text-gray-200 px-5 py-3 text-xs font-black uppercase tracking-widest">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="submitting"
                            :class="submitting ? 'opacity-70 cursor-wait' : 'hover:bg-blue-700 shadow-lg shadow-blue-500/20'"
                            class="rounded-2xl bg-[#1877F2] text-white px-5 py-3 text-xs font-black uppercase tracking-widest flex items-center justify-center gap-2">
                            <i :data-lucide="submitting ? 'loader-circle' : 'save'" class="w-4 h-4" :class="submitting ? 'animate-spin' : ''"></i>
                            <span x-text="submitting ? 'Guardando...' : 'Enviar modificación'"></span>
                        </button>
                    </div>
                </form>

                <aside class="bg-white dark:bg-[#242526] rounded-3xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sm:p-6 h-fit">
                    <h2 class="text-sm font-black uppercase tracking-widest mb-4">Reserva actual</h2>
                    <div class="space-y-3">
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                            <p class="text-[10px] uppercase font-black text-gray-400">Fecha y hora</p>
                            <p class="font-black mt-1">{{ \Carbon\Carbon::parse($reserva->fecha)->format('d/m/Y') }} · {{ $horaActual }}</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                            <p class="text-[10px] uppercase font-black text-gray-400">Asistentes</p>
                            <p class="font-black mt-1">{{ $reserva->cantidad_personas }} persona(s)</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-4">
                            <p class="text-[10px] uppercase font-black text-gray-400">Estado luego del cambio</p>
                            <p class="font-black mt-1 text-amber-500">Pendiente a revisión</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        const editIconPaths = {
            'check-circle-2': '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
            'alert-circle': '<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
            'loader-circle': '<path d="M21 12a9 9 0 1 1-6.2-8.56"/>',
            'save': '<path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8"/><path d="M7 3v5h8"/>',
        };

        function renderEditIcons() {
            document.querySelectorAll('[data-lucide]').forEach((icon) => {
                const name = icon.getAttribute('data-lucide');
                const path = editIconPaths[name];
                if (!path) return;

                icon.style.display = 'inline-flex';
                icon.style.alignItems = 'center';
                icon.style.justifyContent = 'center';
                icon.style.flexShrink = '0';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 100%; height: 100%; max-width: 1.25rem; max-height: 1.25rem; display: block;">${path}</svg>`;
            });
        }

        function editarReserva() {
            return {
                darkMode: localStorage.getItem('theme') === 'dark',
                submitting: false,
                message: '',
                messageType: 'success',
                today: new Date().toISOString().slice(0, 10),
                horarios: @js($horarios),
                reservedSlots: @js($reservedSlots ?? []),
                form: {
                    fecha: @js($fechaActual),
                    hora_inicio: @js($horaActual),
                    cantidad_personas: @js($reserva->cantidad_personas),
                    descripcion: ''
                },
                init() {
                    document.documentElement.classList.toggle('dark', this.darkMode);
                    this.validateDate();
                    this.$nextTick(() => window.lucide ? window.lucide.createIcons() : renderEditIcons());
                },
                isWeekend(dateString) {
                    if (!dateString) return false;
                    const [year, month, day] = dateString.split('-').map(Number);
                    const dayNumber = new Date(year, month - 1, day).getDay();

                    return dayNumber === 0 || dayNumber === 6;
                },
                isSlotOccupied(horario) {
                    const inicio = String(horario || '').slice(0, 5);
                    return Boolean(this.reservedSlots?.[this.form.fecha]?.[inicio]);
                },
                validateDate() {
                    if (!this.form.fecha) return;

                    if (this.isWeekend(this.form.fecha)) {
                        this.messageType = 'error';
                        this.message = 'Selecciona una fecha de lunes a viernes. El observatorio está cerrado sábado y domingo.';
                        this.$nextTick(() => window.lucide ? window.lucide.createIcons() : renderEditIcons());
                        return;
                    }

                    if (this.isSlotOccupied(this.form.hora_inicio)) {
                        const libre = this.horarios.find((horario) => !this.isSlotOccupied(horario));
                        this.form.hora_inicio = libre || '';
                    }

                    if (this.message && this.message.includes('lunes a viernes')) {
                        this.message = '';
                    }
                },
                volver() {
                    @if($embedded)
                        window.parent.postMessage({ type: 'reserva-panel', panel: 'reservas' }, window.location.origin);
                    @else
                        window.location.href = @js(route('dashboard', ['panel' => 'reservas']));
                    @endif
                },
                async submit() {
                    this.message = '';
                    this.validateDate();
                    if (this.messageType === 'error' && this.message) {
                        return;
                    }

                    if (!this.form.hora_inicio || this.isSlotOccupied(this.form.hora_inicio)) {
                        this.messageType = 'error';
                        this.message = 'Ese horario ya está ocupado. Elige una hora marcada como libre.';
                        this.$nextTick(() => window.lucide ? window.lucide.createIcons() : renderEditIcons());
                        return;
                    }

                    if (!this.form.descripcion || this.form.descripcion.trim().length < 10) {
                        this.messageType = 'error';
                        this.message = 'Escribe una observación clara de al menos 10 caracteres.';
                        this.$nextTick(() => window.lucide ? window.lucide.createIcons() : renderEditIcons());
                        return;
                    }

                    this.submitting = true;
                    try {
                        const response = await fetch(@js(route('reservas.update', $reserva)), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': @js(csrf_token()),
                            },
                            body: JSON.stringify(this.form),
                        });
                        const data = await response.json().catch(() => ({}));

                        if (!response.ok || data.success === false) {
                            throw new Error(data.message || 'No se pudo modificar la reserva.');
                        }

                        @if($embedded)
                            window.parent.postMessage({
                                type: 'reserva-updated',
                                panel: 'dashboard',
                                message: data.message || 'Reserva modificada correctamente. Quedó pendiente de revisión por secretaría.',
                                reserva: data.reserva || {}
                            }, window.location.origin);
                        @else
                            window.location.href = @js(route('dashboard', ['panel' => 'dashboard']));
                        @endif
                    } catch (error) {
                        this.messageType = 'error';
                        this.message = error.message;
                        this.submitting = false;
                        this.$nextTick(() => window.lucide ? window.lucide.createIcons() : renderEditIcons());
                    }
                }
            }
        }
    </script>
</x-app-layout>
