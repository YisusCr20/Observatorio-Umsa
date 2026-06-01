<x-app-layout>
    {{-- Dependencias --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- AQUÍ LLAMAMOS A TU SCRIPT EXTERNO CON LA RUTA DE LARAVEL --}}
    <script src="{{ asset('js/lista_colegios.js') }}"></script>

    <style>
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
        class="min-h-screen transition-colors duration-500 font-sans antialiased pb-12" x-cloak>

        {{-- Header --}}
        <header
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

        <div class="max-w-5xl mx-auto mt-8 px-4">

            {{-- STEPPER --}}
            <div
                class="bg-white dark:bg-[#242526] rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 p-4 md:p-6 mb-6 flex items-center justify-between md:justify-center md:gap-4 text-[10px] md:text-xs font-bold overflow-x-auto">
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
                <div x-show="step === 1" x-transition.opacity class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-black capitalize" x-text="nombreMesActual + ' ' + anioActual"></h2>
                            <div class="flex gap-2 text-gray-400">
                                <button @click="cambiarMes(-1)" class="hover:text-blue-600 transition-colors"><i
                                        data-lucide="chevron-left" class="w-5 h-5"></i></button>
                                <button @click="cambiarMes(1)" class="hover:text-blue-600 transition-colors"><i
                                        data-lucide="chevron-right" class="w-5 h-5"></i></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-7 gap-y-4 text-center text-sm font-bold">
                            <div class="text-gray-400">Do</div>
                            <div class="text-gray-400">Lu</div>
                            <div class="text-gray-400">Ma</div>
                            <div class="text-gray-400">Mi</div>
                            <div class="text-gray-400">Ju</div>
                            <div class="text-gray-400">Vi</div>
                            <div class="text-gray-400">Sá</div>
                            <template x-for="b in blankDays" :key="'blank'+b">
                                <div></div>
                            </template>
                            <template x-for="day in daysInMonth" :key="'day'+day">
                                <div class="flex justify-center relative">
                                    <button @click="manejarClickDia(day)" :class="{
                                        'bg-blue-600 text-white shadow-lg shadow-blue-500/30': fechaSel === formatFecha(anioActual, mesActual, day),
                                        'hover:bg-blue-50 dark:hover:bg-[#3A3B3C] text-gray-800 dark:text-gray-200': obtenerTipoDia(anioActual, mesActual, day) === 'habil' && fechaSel !== formatFecha(anioActual, mesActual, day),
                                        'text-red-400 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 font-black': obtenerTipoDia(anioActual, mesActual, day) === 'feriado',
                                        'text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-[#1C1D1E]': obtenerTipoDia(anioActual, mesActual, day) === 'finde',
                                        'text-gray-200 dark:text-gray-700 cursor-not-allowed opacity-40': obtenerTipoDia(anioActual, mesActual, day) === 'pasado'
                                    }" class="w-10 h-10 rounded-xl flex items-center justify-center transition-all z-10"
                                        x-text="day"></button>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4 text-[10px] text-gray-400 text-center font-bold">Atención de Lunes a Viernes
                            (No incluye feriados)</div>
                    </div>

                    <div
                        class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8 flex flex-col">
                        <h2 class="text-xl font-black mb-6"
                            x-text="fechaSel ? `Sesiones para: ${fechaSel}` : 'Horarios Disponibles'"></h2>

                        <div x-show="!fechaSel"
                            class="flex-1 flex flex-col items-center justify-center text-gray-400 opacity-60">
                            <i data-lucide="clock"
                                class="w-12 h-12 mb-4 bg-gray-100 dark:bg-[#3A3B3C] p-3 rounded-2xl"></i>
                            <p class="text-center text-sm font-bold px-8">Selecciona una fecha hábil para ver los
                                horarios.</p>
                        </div>

                        <div x-show="fechaSel" class="space-y-3 flex-1 overflow-y-auto pr-2 scrollbar-hide"
                            style="max-height: 300px;">
                            <template x-for="hora in horarios" :key="hora">
                                <button @click="sesionSel = hora"
                                    :class="sesionSel === hora ? 'ring-2 ring-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-gray-50 dark:hover:bg-[#1C1D1E] border-gray-100 dark:border-gray-800 bg-white dark:bg-[#242526]'"
                                    class="w-full text-left p-4 rounded-xl border-2 transition-all flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0"
                                        :class="sesionSel === hora ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-[#3A3B3C] text-gray-500'">
                                        <i data-lucide="clock" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-black text-sm" x-text="hora"></h3>
                                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">
                                            Visita Guiada</p>
                                    </div>
                                    <div class="ml-auto">
                                        <i data-lucide="check-circle" class="w-5 h-5 text-blue-600"
                                            x-show="sesionSel === hora"></i>
                                    </div>
                                </button>
                            </template>
                        </div>

                        <div class="mt-6 text-right">
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
                    class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
                    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <label class="cursor-pointer group">
                                    <input type="radio" name="tipo_visita" value="Individual" x-model="form.tipo"
                                        @change="calcularTotal()" class="hidden">
                                    <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4"
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
                                    <div class="p-5 rounded-2xl border-2 transition-all flex items-center gap-4"
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
                                class="space-y-6 bg-blue-50/50 dark:bg-[#18191A]/50 p-6 rounded-3xl border border-blue-100 dark:border-gray-800">
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
                    class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8">
                    <div class="mb-8 text-center">
                        <h2 class="text-2xl font-black">Confirmar y Método de Pago</h2>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div
                                class="bg-gray-50 dark:bg-[#18191A] border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
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
                            class="bg-gray-50 dark:bg-[#18191A] border border-gray-200 dark:border-gray-800 rounded-2xl p-6 flex flex-col h-full">
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
                                        class="p-4 border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526] rounded-xl flex items-center gap-4 transition-all">
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
                                    <input type="radio" name="pago" value="efectivo" x-model="metodoPago"
                                        class="hidden">
                                    <div
                                        class="p-4 border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-[#242526] rounded-xl flex items-center gap-4 transition-all">
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
                                    class="mt-4 flex flex-col items-center justify-center p-4 bg-white dark:bg-white rounded-xl shadow-inner border border-gray-200">
                                    <div id="qrcode_final" class="mb-2 p-2 bg-white rounded-lg"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-col-reverse md:flex-row justify-between gap-4">
                        <button @click="step = 2"
                            class="w-full md:w-auto px-5 py-3 rounded-xl font-bold text-sm bg-gray-100 dark:bg-[#3A3B3C] hover:bg-gray-200 transition-all flex justify-center items-center gap-2">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i> Editar Datos
                        </button>
                        <button type="button" @click="enviarReserva()"
                            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 px-6 py-3 rounded-xl font-bold text-sm transition-all flex justify-center items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Confirmar y Enviar Reserva
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

                    <h2 class="text-3xl font-black mb-2 text-center">¡Solicitud Registrada!</h2>
                    <p class="text-gray-500 font-bold mb-6 text-center text-sm">Tu código es: <span
                            class="text-blue-600 font-black" x-text="codigoGenerado"></span></p>

                    <div
                        class="inline-flex items-center px-4 py-2 mb-8 rounded-full bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800 shadow-sm">
                        <span class="relative flex h-3 w-3 mr-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                        <span class="font-bold uppercase tracking-wider text-[10px] md:text-xs">Estado: Pendiente a
                            revisión de secretaria</span>
                    </div>

                    <div class="w-full max-w-sm space-y-4">
                        <button @click="hablarSecretaria()"
                            class="w-full bg-[#25D366] hover:bg-[#20bd5a] text-white py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 shadow-xl shadow-green-500/30 hover:-translate-y-1 transition-all">
                            <i data-lucide="message-circle" class="w-5 h-5"></i> Hablar con la Secretaria
                        </button>

                        <a href="{{ route('dashboard') }}"
                            class="w-full bg-gray-200 dark:bg-[#3A3B3C] hover:bg-gray-300 dark:hover:bg-[#4E4F50] text-gray-700 dark:text-gray-200 py-4 rounded-xl font-black uppercase tracking-widest text-xs flex items-center justify-center gap-3 transition-all">
                            <i data-lucide="home" class="w-5 h-5"></i> Volver al Inicio
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- SCRIPT ALPINE.JS (LÓGICA) --}}
    <script>
        function reservaStepper() {
            const hoy = new Date();
            const diasFeriados = ['2026-01-01', '2026-01-22', '2026-02-16', '2026-02-17', '2026-04-03', '2026-05-01', '2026-06-04', '2026-06-21', '2026-07-16', '2026-08-06', '2026-11-02', '2026-12-25'];
            const fechasExcepcionales = [];

            return {
                darkMode: false, step: 1,
                mesActual: hoy.getMonth(), anioActual: hoy.getFullYear(),
                nombresMeses: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                fechaSel: null, sesionSel: null,
                horarios: ["08:00 - 09:30", "09:30 - 11:00", "11:00 - 12:30", "12:30 - 14:00", "14:00 - 15:30", "15:30 - 17:00", "17:00 - 18:30"],

                form: {
                    nombre: '', correo: '', telefono: '', tipo: 'Individual',
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

                metodoPago: 'qr', codigoGenerado: '',

                init() {
                    const savedTheme = localStorage.getItem('theme');
                    if (savedTheme !== null) { this.darkMode = savedTheme === 'dark'; }
                    this.applyTheme();

                    this.colegiosBaseLaPaz = window.colegiosLaPaz || [];
                    this.colegiosBaseElAlto = window.colegiosElAlto || [];

                    this.calcularTotal();

                    this.$watch('darkMode', () => this.applyTheme());
                    this.$watch('step', () => { this.$nextTick(() => { lucide.createIcons(); if (this.step === 3 && this.metodoPago === 'qr') this.generarQR(); }); });
                    this.$watch('metodoPago', (val) => { if (val === 'qr') this.$nextTick(() => this.generarQR()); });
                },

                applyTheme() { document.documentElement.classList.toggle('dark', this.darkMode); localStorage.setItem('theme', this.darkMode ? 'dark' : 'light'); this.$nextTick(() => lucide.createIcons()); },
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
                    if (tipo === 'feriado') { alert("📅 ¡Atención! Esta fecha es Feriado. El Observatorio estará cerrado."); return; }
                    if (tipo === 'finde') { alert("🔭 El Observatorio no atiende los fines de semana. ¡Te esperamos de lunes a viernes!"); return; }
                    this.seleccionarFecha(dia);
                },
                seleccionarFecha(dia) { this.fechaSel = this.formatFecha(this.anioActual, this.mesActual, dia); this.sesionSel = null; },
                siguientePaso() { if (this.step === 1 && this.fechaSel && this.sesionSel) this.step = 2; },

                seleccionarCiudad(ciudadElegida) {
                    this.form.ciudad = ciudadElegida;
                    this.errores.ciudad = false;
                    this.busquedaColegio = '';
                    this.form.institucion = '';
                    if (ciudadElegida === 'lapaz') this.listaActiva = this.colegiosBaseLaPaz;
                    else if (ciudadElegida === 'elalto') this.listaActiva = this.colegiosBaseElAlto;
                    this.$nextTick(() => lucide.createIcons());
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

                generarQR() {
                    const container = document.getElementById("qrcode_final");
                    if (!container) return; container.innerHTML = "";
                    new QRCode(container, { text: `PAGO|OBSERVATORIO|RES-${this.codigoGenerado || 'TEMP'}|MONTO|${this.totalMonto}`, width: 140, height: 140, colorDark: "#000000", colorLight: "#ffffff" });
                },

                enviarReserva() {
                    if (!this.fechaSel) {
                        alert("❌ Ocurrió un error: La fecha se borró. Por favor vuelve al Paso 1 y selecciona la fecha de nuevo.");
                        this.step = 1;
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const payload = {
                        fecha: this.fechaSel,
                        cantidad_personas: this.totalPersonas,
                        hora_inicio: this.sesionSel,
                        descripcion: 'Reserva ' + this.form.tipo + (this.form.institucion ? ' - ' + this.form.institucion : ''),
                        nombre: this.form.nombre,
                        correo: this.form.correo,
                        telefono: this.form.telefono
                    };

                    alert("⏳ Registrando en la base de datos... Presiona Aceptar");

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

                            if (response.ok || data.success) {
                                if (!window.ultimoIdReserva) window.ultimoIdReserva = 0;
                                window.ultimoIdReserva++;
                                this.codigoGenerado = 'RES-' + window.ultimoIdReserva.toString().padStart(4, '0');
                                this.step = 4;
                                this.$nextTick(() => lucide.createIcons());
                            } else {
                                if (data.errors) {
                                    alert("❌ LARAVEL RECHAZÓ LOS DATOS:\n" + JSON.stringify(data.errors, null, 2));
                                } else if (data.message) {
                                    alert("❌ ERROR GRAVE DE LARAVEL:\n" + data.message);
                                } else {
                                    alert("❌ ERROR DESCONOCIDO.");
                                }
                            }
                        })
                        .catch(error => {
                            alert("❌ Error de comunicación con el servidor: " + error.message);
                        });
                },
                hablarSecretaria() {
                    const celSecretaria = "+59170000000"; // Asegúrate de actualizar este número
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
                }
                
            }
        }
    </script>
</x-app-layout>