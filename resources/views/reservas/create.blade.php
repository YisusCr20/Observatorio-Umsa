<x-app-layout>
    @php
        $embedded = request()->boolean('embedded');
    @endphp
    {{-- Dependencias --}}
    @unless($embedded)
        <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script>
    @endunless
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- AQUÍ LLAMAMOS A TU SCRIPT EXTERNO CON LA RUTA DE LARAVEL --}}
    <script src="{{ asset('js/lista_colegios.js') }}" defer></script>

    <style>
        @if($embedded)
            html,
            body {
                background: #F0F2F5 !important;
                overflow-x: hidden !important;
            }

            html.dark,
            html.dark body {
                background: #18191A !important;
            }

            body > .relative.min-h-screen {
                min-height: auto !important;
                background: #F0F2F5 !important;
            }

            html.dark body > .relative.min-h-screen {
                background: #18191A !important;
            }

            body > .relative.min-h-screen > .fixed {
                display: none !important;
            }

            body > .relative.min-h-screen > .relative.z-10.flex.min-h-screen {
                display: block !important;
                min-height: auto !important;
            }

            body > .relative.min-h-screen > .relative.z-10.flex.min-h-screen > main {
                width: 100% !important;
                overflow: visible !important;
            }
        @endif

        [x-cloak] {
            display: none !important;
        }

        input[type="number"]::-webkit-inner-spin-button,
        input[type="number"]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.3s ease;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }

        .scrollbar-hide::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-hide::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-hide::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
    </style>

    <div x-data="reservaStepper()" x-init="init()"
        :class="darkMode ? 'dark bg-[#18191A] text-gray-100' : 'bg-[#F0F2F5] text-gray-900'"
        class="{{ $embedded ? 'min-h-0 pb-4' : 'min-h-screen pb-12' }} transition-colors duration-500 font-sans antialiased" x-cloak>

        {{-- Header --}}
        <header
            @if($embedded) style="display: none;" @endif
            class="sticky top-0 z-[150] p-4 lg:px-8 backdrop-blur-xl bg-white/70 dark:bg-[#242526]/80 border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-5xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="w-11 h-11 rounded-2xl bg-white dark:bg-[#3A3B3C] shadow-sm border border-gray-100 dark:border-gray-700 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all group">
                        <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                    </a>
                    <div>
                        <h1 class="text-lg md:text-xl font-black tracking-tight uppercase">Nueva Reserva de Visita</h1>
                        <p class="text-[10px] md:text-[11px] font-bold text-gray-500 dark:text-gray-400">Observatorio
                            Astronómico Max Schreier — UMSA</p>
                    </div>
                </div>
                <button @click="toggleTheme()"
                    class="w-11 h-11 flex items-center justify-center rounded-2xl bg-white dark:bg-[#3A3B3C] shadow-sm border border-gray-100 dark:border-gray-700 text-yellow-500 hover:rotate-12 transition-all">
                    <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
                </button>
            </div>
        </header>

        <div class="{{ $embedded ? 'max-w-6xl mt-0 pt-1' : 'max-w-5xl mt-8' }} mx-auto px-2 sm:px-3 lg:px-4">

            {{-- STEPPER --}}
            <div
                class="bg-white dark:bg-[#242526] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 {{ $embedded ? 'p-2.5 md:p-3 mb-3' : 'p-4 md:p-6 mb-6' }} flex items-center justify-between md:justify-center md:gap-4 text-[10px] md:text-xs font-bold overflow-x-auto">
                <div class="flex flex-col items-center gap-2 shrink-0"
                    :class="step >= 1 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-2 transition-all"
                        :class="step > 1 ? 'bg-blue-600 border-blue-600 text-white' : (step === 1 ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700')">
                        <i :data-lucide="step > 1 ? 'check' : 'calendar'" class="w-4 h-4"></i>
                    </div>
                    <span>Fecha y Sesión</span>
                </div>
                <div class="w-full md:w-32 h-[2px] rounded-full mx-2 transition-colors"
                    :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                <div class="flex flex-col items-center gap-2 shrink-0"
                    :class="step >= 2 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-2 transition-all"
                        :class="step > 2 ? 'bg-blue-600 border-blue-600 text-white' : (step === 2 ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700')">
                        <i :data-lucide="step > 2 ? 'check' : 'user'" class="w-4 h-4"></i>
                    </div>
                    <span>Datos y Montos</span>
                </div>
                <div class="w-full md:w-32 h-[2px] rounded-full mx-2 transition-colors"
                    :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-200 dark:bg-gray-700'"></div>
                <div class="flex flex-col items-center gap-2 shrink-0"
                    :class="step >= 3 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400'">
                    <div class="w-8 h-8 md:w-10 md:h-10 rounded-full flex items-center justify-center border-2 transition-all"
                        :class="step === 3 ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/30' : 'border-gray-200 dark:border-gray-700'">
                        <i data-lucide="file-check" class="w-4 h-4"></i>
                    </div>
                    <span>Confirmar Reserva</span>
                </div>
            </div>

            {{-- CONTENIDO --}}
            <div class="relative">

                {{-- PASO 1: CALENDARIO --}}
                <div x-show="step === 1" x-transition.opacity class="grid grid-cols-1 lg:grid-cols-2 {{ $embedded ? 'gap-3 lg:gap-4' : 'gap-6' }}">
                    <div
                        class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 {{ $embedded ? 'p-4 md:p-5' : 'p-6 md:p-8' }}">
                        <div class="flex items-center justify-between {{ $embedded ? 'mb-4' : 'mb-6' }}">
                            <h2 class="{{ $embedded ? 'text-lg' : 'text-xl' }} font-black capitalize" x-text="nombreMesActual + ' ' + anioActual"></h2>
                            <div class="flex gap-2 text-gray-400">
                                <button @click="cambiarMes(-1)" class="hover:text-blue-600 transition-colors"><i
                                        data-lucide="chevron-left" class="w-5 h-5"></i></button>
                                <button @click="cambiarMes(1)" class="hover:text-blue-600 transition-colors"><i
                                        data-lucide="chevron-right" class="w-5 h-5"></i></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 {{ $embedded ? 'gap-y-2.5' : 'gap-y-4' }} text-center text-sm font-bold">
                            <div class="text-slate-500 dark:text-slate-300">Do</div>
                            <div class="text-slate-500 dark:text-slate-300">Lu</div>
                            <div class="text-slate-500 dark:text-slate-300">Ma</div>
                            <div class="text-slate-500 dark:text-slate-300">Mi</div>
                            <div class="text-slate-500 dark:text-slate-300">Ju</div>
                            <div class="text-slate-500 dark:text-slate-300">Vi</div>
                            <div class="text-slate-500 dark:text-slate-300">Sá</div>
                            <template x-for="b in blankDays" :key="'blank'+b">
                                <div></div>
                            </template>
                            <template x-for="day in daysInMonth" :key="'day'+day">
                                <div class="flex justify-center relative">
                                    <button @click="manejarClickDia(day)" :class="{
                                        'bg-blue-600 text-white shadow-lg shadow-blue-500/30': fechaSel === formatFecha(anioActual, mesActual, day),
                                        'bg-red-50 dark:bg-red-900/20 text-red-500 border border-red-100 dark:border-red-800': diaSinHorarios(anioActual, mesActual, day) && fechaSel !== formatFecha(anioActual, mesActual, day),
                                        'bg-slate-50 border border-slate-100 hover:bg-blue-50 dark:bg-[#1C1D1E] dark:border-slate-700 dark:hover:bg-[#3A3B3C] text-slate-950 dark:text-slate-100': obtenerTipoDia(anioActual, mesActual, day) === 'habil' && fechaSel !== formatFecha(anioActual, mesActual, day),
                                        'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 font-black': obtenerTipoDia(anioActual, mesActual, day) === 'feriado',
                                        'bg-slate-200 dark:bg-[#111827] text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700 hover:bg-slate-300 dark:hover:bg-[#1C1D1E]': obtenerTipoDia(anioActual, mesActual, day) === 'finde',
                                        'bg-slate-100 dark:bg-[#111827] text-slate-500 dark:text-slate-500 border border-slate-200 dark:border-slate-700 cursor-not-allowed': obtenerTipoDia(anioActual, mesActual, day) === 'pasado'
                                    }" class="{{ $embedded ? 'w-8 h-8 md:w-9 md:h-9' : 'w-10 h-10' }} rounded-xl flex items-center justify-center transition-all z-10"
                                        x-text="day"></button>
                                </div>
                            </template>
                        </div>
                        <div class="{{ $embedded ? 'mt-3' : 'mt-4' }} text-[10px] text-gray-400 text-center font-bold">Atención de Lunes a Viernes
                            (No incluye feriados)</div>
                    </div>

                    <div
                        class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 {{ $embedded ? 'p-4 md:p-5' : 'p-6 md:p-8' }} flex flex-col">
                        <h2 class="{{ $embedded ? 'text-lg mb-4' : 'text-xl mb-6' }} font-black"
                            x-text="fechaSel ? `Sesiones para: ${fechaSel}` : 'Horarios Disponibles'"></h2>

                        <div x-show="!fechaSel"
                            class="flex-1 flex flex-col items-center justify-center text-gray-400 opacity-60">
                            <i data-lucide="clock"
                                class="w-12 h-12 mb-4 bg-gray-100 dark:bg-[#3A3B3C] p-3 rounded-2xl"></i>
                            <p class="text-center text-sm font-bold px-8">Selecciona una fecha hábil para ver los
                                horarios.</p>
                        </div>

                        <div x-show="fechaSel" class="{{ $embedded ? 'space-y-2' : 'space-y-3' }} flex-1 overflow-y-auto pr-2 scrollbar-hide"
                            style="max-height: {{ $embedded ? '250px' : '300px' }};">
                            <template x-for="hora in horarios" :key="hora">
                                <button @click="seleccionarSesion(hora)"
                                    :disabled="horarioOcupado(hora)"
                                    :class="horarioOcupado(hora)
                                        ? 'opacity-60 cursor-not-allowed bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800'
                                        : (sesionSel === hora ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-[#1C1D1E] border-gray-100 dark:border-gray-800 bg-white dark:bg-[#242526]')"
                                    class="w-full text-left {{ $embedded ? 'p-3' : 'p-4' }} rounded-xl border-2 transition-all flex items-center gap-4">
                                    <div class="{{ $embedded ? 'w-9 h-9' : 'w-10 h-10' }} rounded-lg flex items-center justify-center shrink-0"
                                        :class="horarioOcupado(hora) ? 'bg-red-100 text-red-500 dark:bg-red-900/40' : (sesionSel === hora ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-[#3A3B3C] text-gray-500')">
                                        <i :data-lucide="horarioOcupado(hora) ? 'lock' : 'clock'" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-black text-sm" x-text="hora"></h3>
                                        <p class="text-[10px] font-bold uppercase tracking-wider mt-0.5"
                                            :class="horarioOcupado(hora) ? 'text-red-500' : 'text-emerald-500'"
                                            x-text="horarioOcupado(hora) ? 'Ocupado por otra reserva' : 'Disponible para visita guiada'"></p>
                                    </div>
                                    <div class="ml-auto">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-600"
                                            x-show="sesionSel === hora"></i>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div class="{{ $embedded ? 'mt-4' : 'mt-6' }} text-right">
                            <button @click="siguientePaso()" :disabled="!fechaSel || !sesionSel"
                                :class="(!fechaSel || !sesionSel) ? 'opacity-50 cursor-not-allowed bg-gray-300 dark:bg-gray-700 text-gray-500' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 hover:-translate-y-0.5'"
                                class="w-full md:w-auto px-6 py-3.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 ml-auto">
                                Siguiente Paso <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- PASO 2: DATOS Y ASISTENTES --}}
                <div x-show="step === 2" x-transition.opacity
                    class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 {{ $embedded ? 'p-4 md:p-5' : 'p-6 md:p-8' }}">
                    <div class="{{ $embedded ? 'mb-5' : 'mb-8' }} flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-black">Datos y Asistentes</h2>
                            <p class="text-sm text-gray-500 font-bold">Selecciona el tipo de visita y añade a los
                                asistentes</p>
                        </div>
                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-100 dark:border-blue-800 rounded-2xl p-4 text-center md:text-right min-w-[200px]">
                            <p
                                class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-1">
                                Monto a Cancelar</p>
                            <div class="flex items-baseline justify-center md:justify-end gap-1">
                                <span class="text-3xl font-black text-gray-900 dark:text-white"
                                    x-text="totalMonto"></span>
                                <span class="text-sm font-bold text-gray-500">Bs.</span>
                            </div>
                            <p class="text-[10px] text-gray-500 font-bold mt-1"
                                x-text="`Total asistentes: ${totalPersonas}`"></p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <h3 class="text-[10px] font-black uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-800 pb-2"
                                :class="errores.nombre || errores.correo || errores.telefono ? 'text-red-500 border-red-500' : 'text-gray-400'">
                                Información del Responsable
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold mb-1"
                                        :class="errores.nombre ? 'text-red-500' : ''">Nombre Completo</label>
                                    <input type="text" x-model="form.nombre" @input="errores.nombre = false"
                                        placeholder="Ej: Juan Pérez"
                                        :class="errores.nombre ? 'border-red-500 bg-red-50 focus:border-red-500 dark:bg-red-900/20' : 'border-transparent focus:border-blue-500 bg-gray-50 dark:bg-[#18191A]'"
                                        class="w-full rounded-xl p-3 border-2 outline-none text-sm font-bold transition-all">
                                    <p x-show="errores.nombre" class="text-[10px] text-red-500 font-bold mt-1">Ingresa
                                        al menos un nombre y un apellido.</p>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold mb-1"
                                        :class="errores.correo ? 'text-red-500' : ''">Correo Electrónico</label>
                                    <input type="email" x-model="form.correo" @input="errores.correo = false"
                                        placeholder="tu@correo.com"
                                        :class="errores.correo ? 'border-red-500 bg-red-50 focus:border-red-500 dark:bg-red-900/20' : 'border-transparent focus:border-blue-500 bg-gray-50 dark:bg-[#18191A]'"
                                        class="w-full rounded-xl p-3 border-2 outline-none text-sm font-bold transition-all">
                                    <p x-show="errores.correo" class="text-[10px] text-red-500 font-bold mt-1">Ingresa
                                        un correo electrónico válido.</p>
                                </div>
                                <div class="md:col-span-1">
                                    <label class="block text-xs font-bold mb-1"
                                        :class="errores.telefono ? 'text-red-500' : ''">Teléfono / Celular</label>
                                    <input type="tel" x-model="form.telefono" @input="errores.telefono = false"
                                        placeholder="Ej: 70012345"
                                        :class="errores.telefono ? 'border-red-500 bg-red-50 focus:border-red-500 dark:bg-red-900/20' : 'border-transparent focus:border-blue-500 bg-gray-50 dark:bg-[#18191A]'"
                                        class="w-full rounded-xl p-3 border-2 outline-none text-sm font-bold transition-all">
                                    <p x-show="errores.telefono" class="text-[10px] text-red-500 font-bold mt-1">Ingresa
                                        un número válido (mín. 8 dígitos).</p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3
                                class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 border-b border-gray-100 dark:border-gray-800 pb-2">
                                ¿Qué tipo de visita estás realizando?
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 md:gap-4 mb-6">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="tipo_visita" value="Individual" x-model="form.tipo"
                                        @change="calcularTotal()" class="hidden">
                                    <div class="{{ $embedded ? 'p-4' : 'p-5' }} rounded-2xl border-2 transition-all flex items-center gap-4"
                                        :class="form.tipo === 'Individual' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526] hover:border-blue-300'">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                                            :class="form.tipo === 'Individual' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-[#3A3B3C] text-gray-500'">
                                            <i data-lucide="user" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-base"
                                                :class="form.tipo === 'Individual' ? 'text-blue-700 dark:text-blue-400' : ''">
                                                Visita Particular</h4>
                                            <p class="text-[10px] text-gray-500 font-bold mt-1">Público en general,
                                                familias, amigos.</p>
                                        </div>
                                    </div>
                                </label>

                                <label class="cursor-pointer group">
                                    <input type="radio" name="tipo_visita" value="Institucion" x-model="form.tipo"
                                        @change="calcularTotal()" class="hidden">
                                    <div class="{{ $embedded ? 'p-4' : 'p-5' }} rounded-2xl border-2 transition-all flex items-center gap-4"
                                        :class="form.tipo === 'Institucion' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526] hover:border-blue-300'">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0"
                                            :class="form.tipo === 'Institucion' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-[#3A3B3C] text-gray-500'">
                                            <i data-lucide="building-2" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-black text-base"
                                                :class="form.tipo === 'Institucion' ? 'text-blue-700 dark:text-blue-400' : ''">
                                                Institución Educativa</h4>
                                            <p class="text-[10px] text-gray-500 font-bold mt-1">Colegios, Escuelas o
                                                Universidades.</p>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div x-show="errores.personas"
                                class="mb-4 text-center text-xs text-red-500 font-bold bg-red-50 dark:bg-red-900/20 p-2 rounded-lg border border-red-200 dark:border-red-800">
                                Debes agregar al menos 1 visitante para continuar.
                            </div>

                            <div x-show="form.tipo === 'Individual'" x-transition.opacity>
                                <div
                                    class="bg-gray-50 dark:bg-[#18191A] p-5 rounded-2xl border border-gray-200 dark:border-gray-800 max-w-sm mx-auto flex items-center justify-between shadow-sm">
                                    <div>
                                        <p class="font-black text-base">Cantidad de Personas</p>
                                        <p class="text-blue-600 font-bold text-xs mt-1">10.00 Bs / persona</p>
                                    </div>
                                    <div class="flex items-center gap-3 bg-white dark:bg-[#242526] rounded-xl p-1.5 border border-gray-200 dark:border-gray-700"
                                        :class="errores.personas ? 'ring-2 ring-red-500' : ''">
                                        <button @click="restar('particulares')"
                                            class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"><i
                                                data-lucide="minus" class="w-5 h-5"></i></button>
                                        <input type="number" x-model.number="form.particulares" @input="calcularTotal()"
                                            class="w-10 text-center p-0 border-none bg-transparent font-black text-lg outline-none">
                                        <button @click="sumar('particulares')"
                                            class="w-10 h-10 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"><i
                                                data-lucide="plus" class="w-5 h-5"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div x-show="form.tipo === 'Institucion'" x-transition.opacity
                                class="{{ $embedded ? 'space-y-4 p-4' : 'space-y-6 p-6' }} bg-blue-50/50 dark:bg-[#18191A]/50 rounded-3xl border border-blue-100 dark:border-gray-800">
                                <div>
                                    <label class="block text-xs font-bold mb-2 uppercase tracking-widest"
                                        :class="errores.ciudad ? 'text-red-500' : 'text-blue-600'">1. Selecciona la
                                        ciudad</label>
                                    <div class="flex gap-3">
                                        <button @click="seleccionarCiudad('lapaz')"
                                            :class="form.ciudad === 'lapaz' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 border-blue-600' : 'bg-white dark:bg-[#242526] text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-blue-400'"
                                            class="flex-1 py-3 rounded-xl font-black text-sm border-2 transition-all flex items-center justify-center gap-2">
                                            <i data-lucide="map-pin" class="w-4 h-4"
                                                x-show="form.ciudad === 'lapaz'"></i> La Paz
                                        </button>
                                        <button @click="seleccionarCiudad('elalto')"
                                            :class="form.ciudad === 'elalto' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30 border-blue-600' : 'bg-white dark:bg-[#242526] text-gray-600 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:border-blue-400'"
                                            class="flex-1 py-3 rounded-xl font-black text-sm border-2 transition-all flex items-center justify-center gap-2">
                                            <i data-lucide="map-pin" class="w-4 h-4"
                                                x-show="form.ciudad === 'elalto'"></i> El Alto
                                        </button>
                                    </div>
                                    <p x-show="errores.ciudad" class="text-[10px] text-red-500 font-bold mt-2">Debes
                                        seleccionar una ciudad.</p>
                                </div>

                                <div class="relative" x-show="form.ciudad" x-transition.opacity>
                                    <label class="block text-xs font-bold mb-1 uppercase tracking-widest"
                                        :class="errores.institucion ? 'text-red-500' : 'text-blue-600'">2. Buscar
                                        Institución</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                        </div>
                                        <input type="text" x-model="busquedaColegio" @focus="mostrarDropdown = true"
                                            @click.away="mostrarDropdown = false" @input="errores.institucion = false"
                                            :placeholder="`Escribe el colegio de ${form.ciudad === 'lapaz' ? 'La Paz' : 'El Alto'}...`"
                                            :class="errores.institucion ? 'border-red-500 bg-red-50 focus:border-red-500 dark:bg-red-900/20' : 'border-blue-200 dark:border-gray-700 focus:border-blue-500 bg-white dark:bg-[#242526]'"
                                            class="w-full rounded-xl pl-10 p-3 border-2 outline-none transition-all text-sm font-bold shadow-sm">
                                    </div>
                                    <p x-show="errores.institucion" class="text-[10px] text-red-500 font-bold mt-1">
                                        Busca y selecciona tu institución.</p>

                                    <div x-show="mostrarDropdown && busquedaColegio.length > 0"
                                        class="absolute z-50 w-full bg-white dark:bg-[#242526] border border-gray-200 dark:border-gray-700 rounded-xl mt-1 shadow-xl max-h-48 overflow-y-auto scrollbar-hide py-2">
                                        <template x-for="colegio in colegiosFiltrados" :key="colegio">
                                            <div @click="seleccionarColegio(colegio)"
                                                class="px-4 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer text-sm font-bold text-gray-700 dark:text-gray-300"
                                                x-text="colegio"></div>
                                        </template>
                                        <div x-show="colegiosFiltrados.length === 0"
                                            class="px-4 py-2 text-sm font-bold text-gray-400">No se encontraron
                                            resultados...</div>
                                    </div>

                                    <p x-show="form.institucion"
                                        class="mt-2 text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 p-2 rounded-lg border border-emerald-100 dark:border-emerald-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i> Seleccionado:
                                        <span x-text="form.institucion"></span>
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-white dark:bg-[#242526] p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center shadow-sm"
                                        :class="errores.personas ? 'ring-2 ring-red-500' : ''">
                                        <i data-lucide="users" class="w-6 h-6 text-blue-500 mb-2"></i>
                                        <p class="font-black text-sm">Estudiantes</p>
                                        <p class="text-blue-600 font-bold text-[10px] mb-3">5.00 Bs c/u</p>
                                        <div
                                            class="flex items-center gap-2 bg-gray-50 dark:bg-[#18191A] rounded-xl p-1 border border-gray-100 dark:border-gray-800 w-full justify-center">
                                            <button @click="restar('estudiantes')"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-lg"><i
                                                    data-lucide="minus" class="w-4 h-4"></i></button>
                                            <input type="number" x-model.number="form.estudiantes"
                                                @input="calcularTotal()"
                                                class="w-10 text-center p-0 border-none bg-transparent font-black text-sm outline-none">
                                            <button @click="sumar('estudiantes')"
                                                class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-lg"><i
                                                    data-lucide="plus" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-[#242526] p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center shadow-sm"
                                        :class="errores.personas ? 'ring-2 ring-red-500' : ''">
                                        <i data-lucide="briefcase" class="w-6 h-6 text-emerald-500 mb-2"></i>
                                        <p class="font-black text-sm">Profesores</p>
                                        <p class="text-blue-600 font-bold text-[10px] mb-3">10.00 Bs c/u</p>
                                        <div
                                            class="flex items-center gap-2 bg-gray-50 dark:bg-[#18191A] rounded-xl p-1 border border-gray-100 dark:border-gray-800 w-full justify-center">
                                            <button @click="restar('profesores')"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-lg"><i
                                                    data-lucide="minus" class="w-4 h-4"></i></button>
                                            <input type="number" x-model.number="form.profesores"
                                                @input="calcularTotal()"
                                                class="w-10 text-center p-0 border-none bg-transparent font-black text-sm outline-none">
                                            <button @click="sumar('profesores')"
                                                class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-lg"><i
                                                    data-lucide="plus" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>

                                    <div class="bg-white dark:bg-[#242526] p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center shadow-sm"
                                        :class="errores.personas ? 'ring-2 ring-red-500' : ''">
                                        <i data-lucide="user-check" class="w-6 h-6 text-purple-500 mb-2"></i>
                                        <p class="font-black text-sm">Padres / Adultos</p>
                                        <p class="text-blue-600 font-bold text-[10px] mb-3">10.00 Bs c/u</p>
                                        <div
                                            class="flex items-center gap-2 bg-gray-50 dark:bg-[#18191A] rounded-xl p-1 border border-gray-100 dark:border-gray-800 w-full justify-center">
                                            <button @click="restar('papas')"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-lg"><i
                                                    data-lucide="minus" class="w-4 h-4"></i></button>
                                            <input type="number" x-model.number="form.papas" @input="calcularTotal()"
                                                class="w-10 text-center p-0 border-none bg-transparent font-black text-sm outline-none">
                                            <button @click="sumar('papas')"
                                                class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-lg"><i
                                                    data-lucide="plus" class="w-4 h-4"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" x-model="form.acepta" @change="errores.acepta = false"
                                    class="mt-1 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                <span class="text-xs font-bold leading-relaxed"
                                    :class="errores.acepta ? 'text-red-500' : 'text-gray-500'">
                                    Acepto las Condiciones de Visita del Observatorio. Entiendo que mi reserva es de
                                    carácter preliminar y está sujeta a la validación del pago y disponibilidad de
                                    cupos. Asimismo, comprendo que las observaciones astronómicas dependen estrictamente
                                    de las condiciones meteorológicas y me comprometo a ser puntual y respetar las
                                    normas de las instalaciones.
                                </span>
                            </label>
                            <p x-show="errores.acepta" class="text-[10px] text-red-500 font-bold mt-1 ml-7">Para
                                continuar debes aceptar los términos.</p>
                        </div>
                    </div>

                    <div
                        class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-col-reverse md:flex-row justify-between gap-4">
                        <button @click="step = 1"
                            class="w-full md:w-auto px-5 py-3 rounded-xl font-bold text-sm text-gray-500 hover:bg-gray-100 dark:hover:bg-[#3A3B3C] transition-all flex justify-center items-center gap-2">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i> Volver a Horarios
                        </button>
                        <button @click="validarPaso2()"
                            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 px-6 py-3 rounded-xl font-bold text-sm transition-all flex justify-center items-center gap-2">
                            Revisar y Pagar <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                {{-- PASO 3: CONFIRMACIÓN Y PAGO --}}
                <div x-show="step === 3" x-transition.opacity
                    class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 {{ $embedded ? 'p-4 md:p-5' : 'p-6 md:p-8' }}">
                    <div class="{{ $embedded ? 'mb-5' : 'mb-8' }} text-center">
                        <h2 class="{{ $embedded ? 'text-xl' : 'text-2xl' }} font-black">Confirmar y Método de Pago</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 {{ $embedded ? 'gap-4' : 'gap-8' }}">
                        <div class="space-y-6">
                            <div
                                class="bg-gray-50 dark:bg-[#18191A] border border-gray-200 dark:border-gray-800 rounded-2xl {{ $embedded ? 'p-4' : 'p-6' }}">
                                <h3
                                    class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-3 h-3"></i> Agenda
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-400 font-bold mb-1 text-[10px] uppercase">Fecha y Hora</p>
                                        <p class="font-black" x-text="`${fechaSel}`"></p>
                                        <p class="font-bold text-blue-600 text-xs mt-0.5" x-text="sesionSel"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 font-bold mb-1 text-[10px] uppercase">Responsable</p>
                                        <p class="font-black truncate" x-text="form.nombre"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-400 font-bold mb-1 text-[10px] uppercase">Visitantes</p>
                                        <p class="font-black" x-text="totalPersonas + ' personas'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-gray-50 dark:bg-[#18191A] border border-gray-200 dark:border-gray-800 rounded-2xl {{ $embedded ? 'p-4' : 'p-6' }} flex flex-col h-full">
                            <div
                                class="flex justify-between items-center mb-6 border-b border-gray-200 dark:border-gray-700 pb-4">
                                <h3
                                    class="text-xs font-black text-gray-800 dark:text-gray-200 uppercase tracking-widest">
                                    Total a Pagar</h3>
                                <div class="text-right">
                                    <p class="text-2xl font-black text-blue-600" x-text="`${totalMonto} Bs.`"></p>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase"
                                        x-text="`${totalPersonas} asistentes en total`"></p>
                                </div>
                            </div>
                            <div class="space-y-4 flex-1">
                                <label class="payment-option block cursor-pointer">
                                    <input type="radio" name="pago" value="qr" x-model="metodoPago" class="hidden">
                                    <div
                                        :class="metodoPago === 'qr' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526]'"
                                        class="p-4 border-2 rounded-xl flex items-center gap-4 transition-all">
                                        <div
                                            class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center shrink-0">
                                            <i data-lucide="qr-code"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-sm">Pago por QR Rápido</p>
                                            <p class="text-[10px] font-bold text-gray-500 mt-0.5">Escanea desde tu banca
                                                móvil</p>
                                        </div>
                                    </div>
                                </label>
                                <label class="payment-option block cursor-pointer">
                                    <input type="radio" name="pago" value="whatsapp" x-model="metodoPago"
                                        class="hidden">
                                    <div
                                        :class="metodoPago === 'whatsapp' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526]'"
                                        class="p-4 border-2 rounded-xl flex items-center gap-4 transition-all">
                                        <div
                                            class="w-10 h-10 bg-green-500/10 text-green-500 rounded-xl flex items-center justify-center shrink-0">
                                            <i data-lucide="message-circle"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-sm">Coordinar por WhatsApp</p>
                                            <p class="text-[10px] font-bold text-gray-500 mt-0.5">Contacta directo a
                                                Secretaría</p>
                                        </div>
                                    </div>
                                </label>
                                <div x-show="metodoPago === 'qr'"
                                    x-transition
                                    class="mt-4 flex flex-col items-center justify-center p-4 bg-white dark:bg-white rounded-xl shadow-inner border border-gray-200">
                                    <img src="{{ asset('images/qr-banco-union.jpeg') }}" alt="QR Banco Unión"
                                        class="w-full max-w-[260px] rounded-xl border border-gray-200 bg-white object-contain">
                                    <p class="mt-2 text-[10px] font-black uppercase tracking-widest text-gray-500">Escanea el QR y conserva tu comprobante.</p>
                                    <p class="mt-2 text-xs font-bold text-gray-500 text-center">
                                        Después de pagar, confirma la reserva y se abrirá un mensaje para enviar el comprobante a secretaría.
                                    </p>
                                </div>
                                <div x-show="metodoPago === 'whatsapp'"
                                    x-transition
                                    class="mt-4 rounded-2xl border border-emerald-100 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 p-4">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-2">Coordinación con secretaría</p>
                                    <p class="text-sm font-bold text-gray-600 dark:text-gray-300">
                                        Al confirmar, la reserva se registrará y se abrirá WhatsApp automáticamente con el mensaje listo para secretaría.
                                    </p>
                                    <p class="mt-3 text-xs font-black text-emerald-700 dark:text-emerald-300">
                                        WhatsApp: <span x-text="secretariaTelefonoVisible"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-col-reverse md:flex-row justify-between gap-4">
                        <div x-show="formError" x-transition
                            class="md:order-2 w-full md:max-w-md rounded-2xl border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 px-4 py-3 text-sm font-bold text-red-600 dark:text-red-300 flex items-start gap-2">
                            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                            <span x-text="formError"></span>
                        </div>
                        <button @click="step = 2"
                            class="w-full md:w-auto px-5 py-3 rounded-xl font-bold text-sm bg-gray-100 dark:bg-[#3A3B3C] hover:bg-gray-200 transition-all flex justify-center items-center gap-2">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i> Editar Datos
                        </button>
                        <button type="button" @click="enviarReserva()" :disabled="enviando"
                            :class="enviando ? 'opacity-70 cursor-wait' : 'hover:bg-blue-700 shadow-lg shadow-blue-500/30'"
                            class="w-full md:w-auto bg-blue-600 text-white px-6 py-3 rounded-xl font-bold text-sm transition-all flex justify-center items-center gap-2">
                            <i :data-lucide="enviando ? 'loader-circle' : 'check-circle'" class="w-4 h-4" :class="enviando ? 'animate-spin' : ''"></i>
                            <span x-text="enviando ? 'Registrando...' : textoBotonConfirmar"></span>
                        </button>
                    </div>
                </div>

                {{-- PASO 4: ÉXITO --}}
                <div x-show="step === 4" x-transition.opacity
                    class="flex flex-col items-center justify-center py-10 px-4">
                    <div class="relative mb-6">
                        <div class="w-20 h-20 bg-emerald-500/20 rounded-full animate-ping absolute"></div>
                        <div
                            class="w-20 h-20 bg-gradient-to-tr from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center relative shadow-xl shadow-emerald-500/30">
                            <i data-lucide="check" class="w-10 h-10 text-white"></i>
                        </div>
                    </div>

                    <h2 class="text-3xl font-black mb-2 text-center">¡Reserva registrada!</h2>
                    <p class="text-gray-500 font-bold mb-6 text-center text-sm">Tu código es: <span
                            class="text-blue-600 font-black" x-text="codigoGenerado"></span></p>

                    <div
                        class="inline-flex items-center px-4 py-2 mb-8 rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800 shadow-sm">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                        <span class="font-bold uppercase tracking-wider text-[10px] md:text-xs">Ponte en contacto con secretaría para confirmar el pago</span>
                    </div>

                    <div class="w-full max-w-md rounded-3xl bg-white dark:bg-[#242526] border border-gray-100 dark:border-gray-800 shadow-sm p-5 mb-5 text-left">
                        <p class="text-[10px] uppercase tracking-widest font-black text-gray-400 mb-3">Resumen enviado</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-3">
                                <p class="text-[10px] uppercase font-black text-gray-400">Fecha</p>
                                <p class="font-black" x-text="fechaSel"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-3">
                                <p class="text-[10px] uppercase font-black text-gray-400">Hora</p>
                                <p class="font-black" x-text="sesionSel"></p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-3">
                                <p class="text-[10px] uppercase font-black text-gray-400">Personas</p>
                                <p class="font-black"><span x-text="totalPersonas"></span> visitante(s)</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 dark:bg-[#18191A] p-3">
                                <p class="text-[10px] uppercase font-black text-gray-400">Monto</p>
                                <p class="font-black"><span x-text="totalMonto"></span> Bs.</p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full max-w-md space-y-3">
                        <button x-show="metodoPago === 'qr'" x-cloak @click="notificarSecretaria(true)"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 shadow-xl shadow-blue-500/30 hover:-translate-y-1 transition-all">
                            <i data-lucide="mail" class="w-5 h-5"></i> Enviar comprobante a secretaría
                        </button>
                        <button @click="hablarSecretaria(true)"
                            :disabled="!whatsappUrl"
                            class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 shadow-xl shadow-green-500/30 hover:-translate-y-1 transition-all">
                            <i data-lucide="message-circle" class="w-5 h-5"></i> Coordinar por WhatsApp
                        </button>

                        @if($embedded)
                            <button type="button"
                                @click="volverDashboard()"
                                class="w-full bg-gray-200 dark:bg-[#3A3B3C] hover:bg-gray-300 dark:hover:bg-[#4E4F50] text-gray-700 dark:text-gray-200 py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 transition-all">
                                <i data-lucide="home" class="w-5 h-5"></i> Volver al dashboard
                            </button>
                        @else
                            <a href="{{ route('dashboard', ['panel' => 'dashboard']) }}"
                                class="w-full bg-gray-200 dark:bg-[#3A3B3C] hover:bg-gray-300 dark:hover:bg-[#4E4F50] text-gray-700 dark:text-gray-200 py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 transition-all">
                                <i data-lucide="home" class="w-5 h-5"></i> Volver al dashboard
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- SCRIPT ALPINE.JS (LÓGICA) --}}
    <script>
        const reservaIconPaths = {
            'arrow-left': '<path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>',
            'sun': '<circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/>',
            'moon': '<path d="M12 3a6 6 0 0 0 9 7.4A9 9 0 1 1 12 3z"/>',
            'check': '<path d="M20 6 9 17l-5-5"/>',
            'calendar': '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
            'user': '<path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>',
            'file-check': '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/>',
            'chevron-left': '<path d="m15 18-6-6 6-6"/>',
            'chevron-right': '<path d="m9 18 6-6-6-6"/>',
            'clock': '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
            'lock': '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
            'check-circle': '<path d="M22 11.1V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
            'building-2': '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18"/><path d="M6 12H4a2 2 0 0 0-2 2v8h20v-8a2 2 0 0 0-2-2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>',
            'minus': '<path d="M5 12h14"/>',
            'plus': '<path d="M12 5v14"/><path d="M5 12h14"/>',
            'map-pin': '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
            'search': '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
            'users': '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'briefcase': '<rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M2 13h20"/>',
            'user-check': '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
            'qr-code': '<rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/>',
            'message-circle': '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
            'alert-circle': '<circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>',
            'loader-circle': '<path d="M21 12a9 9 0 1 1-6.2-8.56"/>',
            'mail': '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-10 6L2 7"/>',
            'home': '<path d="m3 11 9-8 9 8"/><path d="M5 10v10h14V10"/><path d="M9 20v-6h6v6"/>',
        };

        function renderReservaIcons() {
            document.querySelectorAll('[data-lucide]').forEach((icon) => {
                const name = icon.getAttribute('data-lucide');
                const path = reservaIconPaths[name];
                if (!path) return;

                icon.style.display = 'inline-flex';
                icon.style.alignItems = 'center';
                icon.style.justifyContent = 'center';
                icon.style.flexShrink = '0';
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="width: 100%; height: 100%; max-width: 1.25rem; max-height: 1.25rem; display: block;">${path}</svg>`;
            });
        }

        function reservaStepper() {
            const hoy = new Date();
            const secretariaEmail = @js(optional($secretariaContacto)->email ?? config('mail.from.address'));
            const secretariaTelefono = @js(optional($secretariaContacto)->telefono ?: '78837658');
            const diasFeriados = ['2026-01-01', '2026-01-22', '2026-02-16', '2026-02-17', '2026-04-03', '2026-05-01', '2026-06-04', '2026-06-21', '2026-07-16', '2026-08-06', '2026-11-02', '2026-12-25'];
            const fechasExcepcionales = [];
            let disponibilidadReservas = @js($reservedSlots ?? []);
            const usuarioActual = @js([
                'nombre' => auth()->user()->name,
                'correo' => auth()->user()->email,
                'telefono' => auth()->user()->telefono ?? '',
            ]);
            const horariosBase = ['08:30 - 10:00', '10:00 - 11:30', '11:30 - 13:00', '13:00 - 14:30', '14:30 - 16:00', '16:00 - 17:30', '17:30 - 19:00'];

            return {
                darkMode: false, step: 1,
                mesActual: hoy.getMonth(), anioActual: hoy.getFullYear(),
                nombresMeses: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                fechaSel: null, sesionSel: null,
                horarios: horariosBase,

                form: {
                    nombre: usuarioActual.nombre || '', correo: usuarioActual.correo || '', telefono: usuarioActual.telefono || '', tipo: 'Individual',
                    ciudad: null, particulares: 0, estudiantes: 0, profesores: 0, papas: 0,
                    institucion: '', acepta: false
                },

                errores: {
                    nombre: false, correo: false, telefono: false,
                    ciudad: false, institucion: false, personas: false, acepta: false
                },

                totalMonto: 0, totalPersonas: 0,
                busquedaColegio: '', mostrarDropdown: false,

                colegiosBaseLaPaz: [], colegiosBaseElAlto: [], listaActiva: [],

                metodoPago: '', codigoGenerado: '', whatsappUrl: '', enviando: false, formError: '',
                iconRefreshQueued: false,
                secretariaTelefonoVisible: secretariaTelefono || 'Sin teléfono registrado',

                get textoBotonConfirmar() {
                    if (this.metodoPago === 'qr') {
                        return 'Confirmar pago y enviar comprobante';
                    }

                    if (this.metodoPago === 'whatsapp') {
                        return 'Crear reserva y coordinar por WhatsApp';
                    }

                    return 'Confirmar y Enviar Reserva';
                },

                init() {
                    const savedTheme = localStorage.getItem('theme');
                    if (savedTheme !== null) { this.darkMode = savedTheme === 'dark'; }
                    this.applyTheme();

                    const cargarColegios = () => {
                        this.colegiosBaseLaPaz = window.colegiosLaPaz || [];
                        this.colegiosBaseElAlto = window.colegiosElAlto || [];
                        if (this.form.ciudad === 'lapaz') this.listaActiva = this.colegiosBaseLaPaz;
                        if (this.form.ciudad === 'elalto') this.listaActiva = this.colegiosBaseElAlto;
                    };

                    cargarColegios();
                    window.addEventListener('colegios-loaded', cargarColegios, { once: true });

                    this.calcularTotal();

                    this.$watch('darkMode', () => this.applyTheme());
                    this.$watch('step', () => this.refreshIcons());
                    this.$watch('metodoPago', () => { this.formError = ''; this.refreshIcons(); });
                    this.refreshIcons();
                    this.cargarDisponibilidad();
                },

                async cargarDisponibilidad() {
                    try {
                        const response = await fetch(@js(route('reservas.disponibilidad')), {
                            headers: { 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        disponibilidadReservas = data.slots || {};
                    } catch (error) {
                        console.warn('No se pudo cargar disponibilidad de reservas.', error);
                    }
                },

                refreshIcons() {
                    if (this.iconRefreshQueued) return;
                    this.iconRefreshQueued = true;
                    requestAnimationFrame(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        } else {
                            renderReservaIcons();
                        }
                        this.iconRefreshQueued = false;
                    });
                },

                applyTheme() { document.documentElement.classList.toggle('dark', this.darkMode); localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); this.refreshIcons(); },
                toggleTheme() { this.darkMode = !this.darkMode; },

                get nombreMesActual() { return this.nombresMeses[this.mesActual]; },
                get daysInMonth() { return Array.from({ length: new Date(this.anioActual, this.mesActual + 1, 0).getDate() }, (_, i) => i + 1); },
                get blankDays() { return Array.from({ length: new Date(this.anioActual, this.mesActual, 1).getDay() }, (_, i) => i); },

                cambiarMes(mov) {
                    this.mesActual += mov;
                    if (this.mesActual > 11) { this.mesActual = 0; this.anioActual++; }
                    if (this.mesActual < 0) { this.mesActual = 11; this.anioActual--; }
                    this.fechaSel = null; this.sesionSel = null;
                },
                formatFecha(a, m, d) { return `${a}-${(m + 1).toString().padStart(2, '0')}-${d.toString().padStart(2, '0')}`; },
                obtenerTipoDia(anio, mes, dia) {
                    const fechaEval = new Date(anio, mes, dia);
                    const hoyMidnight = new Date(); hoyMidnight.setHours(0, 0, 0, 0);
                    const fechaStr = this.formatFecha(anio, mes, dia);
                    const diaSem = fechaEval.getDay();

                    if (fechaEval < hoyMidnight) return 'pasado';
                    if (fechasExcepcionales.includes(fechaStr)) return 'habil';
                    if (diasFeriados.includes(fechaStr)) return 'feriado';
                    if (diaSem === 0 || diaSem === 6) return 'finde';
                    return 'habil';
                },
                manejarClickDia(dia) {
                    const tipo = this.obtenerTipoDia(this.anioActual, this.mesActual, dia);
                    if (tipo === 'pasado') return;
                    if (tipo === 'feriado') { this.formError = 'Esta fecha es feriado. El Observatorio estará cerrado.'; return; }
                    if (tipo === 'finde') { this.formError = 'El Observatorio no atiende sábados ni domingos. Te esperamos de lunes a viernes.'; return; }
                    this.seleccionarFecha(dia);
                },
                seleccionarFecha(dia) { this.fechaSel = this.formatFecha(this.anioActual, this.mesActual, dia); this.sesionSel = null; },
                siguientePaso() { if (this.step === 1 && this.fechaSel && this.sesionSel) this.step = 2; },
                inicioHora(hora) { return String(hora).slice(0, 5); },
                horarioOcupado(hora) {
                    if (!this.fechaSel) return false;
                    return Boolean(disponibilidadReservas[this.fechaSel]?.[this.inicioHora(hora)]?.ocupado);
                },
                diaSinHorarios(anio, mes, dia) {
                    const fecha = this.formatFecha(anio, mes, dia);
                    if (this.obtenerTipoDia(anio, mes, dia) !== 'habil') return false;
                    return this.horarios.length > 0 && this.horarios.every((hora) => Boolean(disponibilidadReservas[fecha]?.[this.inicioHora(hora)]?.ocupado));
                },
                seleccionarSesion(hora) {
                    if (this.horarioOcupado(hora)) {
                        this.sesionSel = null;
                        return;
                    }
                    this.sesionSel = hora;
                    this.refreshIcons();
                },

                seleccionarCiudad(ciudadElegida) {
                    this.form.ciudad = ciudadElegida;
                    this.errores.ciudad = false;
                    this.busquedaColegio = '';
                    this.form.institucion = '';
                    if (ciudadElegida === 'lapaz') this.listaActiva = this.colegiosBaseLaPaz;
                    else if (ciudadElegida === 'elalto') this.listaActiva = this.colegiosBaseElAlto;
                    this.refreshIcons();
                },

                get colegiosFiltrados() {
                    if (this.busquedaColegio.trim() === '') return [];
                    return this.listaActiva.filter(c => c.toLowerCase().includes(this.busquedaColegio.toLowerCase())).slice(0, 10);
                },
                seleccionarColegio(c) { this.form.institucion = c; this.errores.institucion = false; this.busquedaColegio = c; this.mostrarDropdown = false; },

                sumar(campo) { this.form[campo]++; this.errores.personas = false; this.calcularTotal(); },
                restar(campo) { if (this.form[campo] > 0) this.form[campo]--; this.calcularTotal(); },

                calcularTotal() {
                    if (this.form.tipo === 'Individual') {
                        let part = parseInt(this.form.particulares) || 0;
                        this.totalPersonas = part;
                        this.totalMonto = part * 10;

                        this.form.estudiantes = 0; this.form.profesores = 0; this.form.papas = 0;
                        this.form.institucion = ''; this.busquedaColegio = ''; this.form.ciudad = null;
                    } else {
                        let e = parseInt(this.form.estudiantes) || 0;
                        let p = parseInt(this.form.profesores) || 0;
                        let a = parseInt(this.form.papas) || 0;

                        this.totalPersonas = e + p + a;
                        this.totalMonto = (e * 5) + (p * 10) + (a * 10);
                        this.form.particulares = 0;
                    }
                },

                validarPaso2() {
                    // Resetear errores
                    this.errores = { nombre: false, correo: false, telefono: false, ciudad: false, institucion: false, personas: false, acepta: false };
                    let valido = true;

                    // 1. VALIDAR NOMBRE
                    const nombreLimpio = this.form.nombre.trim();
                    if (!nombreLimpio || nombreLimpio.split(/\s+/).length < 2) {
                        this.errores.nombre = true;
                        valido = false;
                    }

                    // 2. VALIDAR CORREO
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!this.form.correo.trim() || !emailRegex.test(this.form.correo.trim())) {
                        this.errores.correo = true;
                        valido = false;
                    }

                    // 3. VALIDAR TELÉFONO
                    const telefonoLimpio = this.form.telefono.replace(/\s+/g, '');
                    const telRegex = /^\+?[0-9]{8,15}$/;
                    if (!telefonoLimpio || !telRegex.test(telefonoLimpio)) {
                        this.errores.telefono = true;
                        valido = false;
                    }

                    // 4. Validar cantidad de personas
                    if (this.totalPersonas < 1) {
                        this.errores.personas = true;
                        valido = false;
                    }

                    // 5. Validar Institución
                    if (this.form.tipo === 'Institucion') {
                        if (!this.form.ciudad) { this.errores.ciudad = true; valido = false; }
                        if (!this.form.institucion.trim()) { this.errores.institucion = true; valido = false; }
                    }

                    // 6. Validar términos y condiciones
                    if (!this.form.acepta) {
                        this.errores.acepta = true;
                        valido = false;
                    }

                    // Si todo está perfecto, pasa al paso 3 (ELIMINADA LA DUPLICACIÓN QUE HABÍA AQUÍ)
                    if (valido) {
                        this.step = 3;
                    }
                },

                enviarReserva() {
                    if (this.enviando) return;
                    this.formError = '';
                    if (!this.fechaSel) {
                        this.formError = 'La fecha se borró. Vuelve al paso 1 y selecciona una fecha.';
                        this.step = 1;
                        return;
                    }
                    if (!this.sesionSel || this.horarioOcupado(this.sesionSel)) {
                        this.formError = 'Ese horario ya está ocupado. Selecciona otra hora disponible.';
                        this.step = 1;
                        return;
                    }
                    if (!this.metodoPago) {
                        this.formError = 'Selecciona un método de pago: QR o coordinación por WhatsApp.';
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    this.enviando = true;

                    const payload = {
                        fecha: this.fechaSel,
                        cantidad_personas: this.totalPersonas,
                        hora_inicio: this.sesionSel,
                        descripcion: 'Reserva ' + this.form.tipo + (this.form.institucion ? ' - ' + this.form.institucion : ''),
                        nombre: this.form.nombre,
                        correo: this.form.correo,
                        telefono: this.form.telefono
                    };

                    fetch("{{ route('reservas.store') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": token,
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify(payload)
                    })
                        .then(async response => {
                            let data = await response.json();

                            if (response.ok && data.success) {
                                this.codigoGenerado = data.codigo || ('RES-' + String(data.reserva?.id || Date.now()).padStart(4, '0'));
                                this.whatsappUrl = data.whatsapp_url || '';
                                this.step = 4;
                                this.$nextTick(() => {
                                    this.refreshIcons();
                                });
                            } else {
                                if (data.errors) {
                                    this.formError = 'Laravel rechazó los datos. Revisa los campos del formulario.';
                                } else if (data.message) {
                                    this.formError = data.message;
                                } else {
                                    this.formError = 'No se pudo crear la reserva. Intenta nuevamente.';
                                }
                            }
                        })
                        .catch(error => {
                            this.formError = 'Error de comunicación con el servidor: ' + error.message;
                        })
                        .finally(() => {
                            this.enviando = false;
                            this.refreshIcons();
                        });
                },
                volverDashboard() {
                    @if($embedded)
                        window.parent.postMessage({
                            type: 'reserva-created',
                            panel: 'dashboard',
                            message: 'Reserva registrada correctamente. Quedó pendiente de confirmación por secretaría.',
                            codigo: this.codigoGenerado,
                            whatsapp_url: this.whatsappUrl
                        }, window.location.origin);
                    @else
                        window.location.href = @js(route('dashboard', ['panel' => 'dashboard']));
                    @endif
                },
                hablarSecretaria(volverDespues = false) {
                    if (this.whatsappUrl) {
                        window.open(this.whatsappUrl, '_blank');
                        if (volverDespues) this.volverDashboard();
                        return;
                    }

                    const telefonoLimpio = String(secretariaTelefono || '').replace(/\D+/g, '');
                    const celSecretaria = telefonoLimpio.startsWith('591') ? telefonoLimpio : `591${telefonoLimpio}`;
                    let detalleAsistentes = [];

                    if (this.form.tipo === 'Individual') {
                        detalleAsistentes.push(`${this.form.particulares} Personas (Público General)`);
                    } else {
                        if (this.form.estudiantes > 0) detalleAsistentes.push(`${this.form.estudiantes} Estudiantes`);
                        if (this.form.profesores > 0) detalleAsistentes.push(`${this.form.profesores} Profesores`);
                        if (this.form.papas > 0) detalleAsistentes.push(`${this.form.papas} Padres/Adultos`);
                    }

                    const ciudadTxt = this.form.ciudad === 'lapaz' ? 'La Paz' : 'El Alto';
                    const instTxt = this.form.tipo === 'Institucion' ? `*Institución (${ciudadTxt}):* ${this.form.institucion}%0A` : '';
                    const msjPago = this.metodoPago === 'qr' ? 'Adjunto el comprobante de pago por QR.' : 'Deseo coordinar el pago en efectivo.';

                    const texto = `*RESERVA OBSERVATORIO*%0A` +
                        `*Código:* ${this.codigoGenerado}%0A` +
                        `*A nombre de:* ${this.form.nombre}%0A` +
                        `*Fecha y Hora:* ${this.fechaSel} / ${this.sesionSel}%0A` + instTxt +
                        `*Desglose:* ${detalleAsistentes.join(', ')}%0A` +
                        `*Total Calculado:* ${this.totalMonto} Bs.%0A%0A` +
                        `*Mensaje:* Hola, ${msjPago}`;

                    window.open(`https://wa.me/${celSecretaria}?text=${texto}`, '_blank');
                    if (volverDespues) this.volverDashboard();
                },
                notificarSecretaria(volverDespues = false) {
                    const detalleAsistentes = this.form.tipo === 'Individual'
                        ? `${this.form.particulares} Personas (Público General)`
                        : `${this.form.estudiantes} Estudiantes, ${this.form.profesores} Profesores, ${this.form.papas} Padres/Adultos`;
                    const institucion = this.form.tipo === 'Institucion' ? `Institución: ${this.form.institucion}\n` : '';
                    const subject = encodeURIComponent(`Reserva ${this.codigoGenerado} - Observatorio Max Schreier`);
                    const body = encodeURIComponent(
                        `Hola Secretaría,\n\nSe registró una nueva solicitud de reserva.\n\n` +
                        `Código: ${this.codigoGenerado}\n` +
                        `Responsable: ${this.form.nombre}\n` +
                        `Correo: ${this.form.correo}\n` +
                        `Teléfono: ${this.form.telefono}\n` +
                        `Fecha y hora: ${this.fechaSel} / ${this.sesionSel}\n` +
                        institucion +
                        `Asistentes: ${detalleAsistentes}\n` +
                        `Total calculado: ${this.totalMonto} Bs.\n` +
                        `Método: ${this.metodoPago === 'qr' ? 'QR' : 'Coordinar por WhatsApp'}\n\n` +
                        (this.metodoPago === 'qr'
                            ? `Adjunto el comprobante del pago por QR para su validación.\n\n`
                            : `Solicito coordinar el pago directamente por WhatsApp.\n\n`) +
                        `Queda pendiente de validación por secretaría.`
                    );

                    window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=${encodeURIComponent(secretariaEmail)}&su=${subject}&body=${body}`, '_blank');
                    if (volverDespues) this.volverDashboard();
                }
                
            }
        }
    </script>
</x-app-layout>
