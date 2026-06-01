<x-app-layout>
    {{-- Dependencias --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
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
                    <a href="{{ route('reservas.index') }}"
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

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white dark:bg-[#242526] p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center shadow-sm"
                                        :class="errores.personas ? 'ring-2 ring-red-500' : ''">
                                        <i data-lucide="users" class="w-6 h-6 text-blue-500 mb-2"></i>
                                        <p class="font-black text-sm">Estudiantes</p>
                                        <p class="text-blue-600 font-bold text-[10px] mb-3">5.00 Bs c/u</p>
                                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-[#18191A] rounded-xl p-1 border border-gray-100 dark:border-gray-800 w-full justify-center">
                                            <button @click="restar('estudiantes')"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-lg">
                                                <i data-lucide="minus" class="w-4 h-4"></i>
                                            </button>
                                            <input type="number" x-model.number="form.estudiantes"
                                                @input="calcularTotal()"
                                                class="w-10 text-center p-0 border-none bg-transparent font-black text-sm outline-none">
                                            <button @click="sumar('estudiantes')"
                                                class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-lg">
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-[#242526] p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center shadow-sm">
                                        <i data-lucide="user-check" class="w-6 h-6 text-emerald-500 mb-2"></i>
                                        <p class="font-black text-sm">Docentes / Adultos</p>
                                        <p class="text-emerald-600 font-bold text-[10px] mb-3">10.00 Bs c/u</p>
                                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-[#18191A] rounded-xl p-1 border border-gray-100 dark:border-gray-800 w-full justify-center">
                                            <button @click="restar('docentes')"
                                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-lg">
                                                <i data-lucide="minus" class="w-4 h-4"></i>
                                            </button>
                                            <input type="number" x-model.number="form.docentes"
                                                @input="calcularTotal()"
                                                class="w-10 text-center p-0 border-none bg-transparent font-black text-sm outline-none">
                                            <button @click="sumar('docentes')"
                                                class="w-8 h-8 flex items-center justify-center text-emerald-600 hover:bg-emerald-100 rounded-lg">
                                                <i data-lucide="plus" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between border-t border-gray-100 dark:border-gray-800 pt-6">
                            <button @click="pasoAnterior()"
                                class="px-6 py-3.5 rounded-xl font-bold text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#3A3B3C] transition-all flex items-center gap-2">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i> Atrás
                            </button>
                            <button @click="siguientePaso()"
                                class="bg-blue-600 hover:bg-blue-700 text-white shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 px-6 py-3.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                                Revisar Reserva <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- PASO 3: CONFIRMAR RESERVA --}}
                <div x-show="step === 3" x-transition.opacity
                    class="bg-white dark:bg-[#242526] rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 p-6 md:p-8 max-w-2xl mx-auto">
                    
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="clipboard-check" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black">Confirma tu Reserva</h2>
                        <p class="text-sm text-gray-500 font-bold mt-1">Revisa que todos tus datos sean correctos antes de finalizar.</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-[#18191A] rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-8 space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Fecha y Hora</p>
                                <p class="font-black text-lg" x-text="fechaSel + ' — ' + sesionSel"></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 border-b border-gray-200 dark:border-gray-700 pb-4">
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Responsable</p>
                                <p class="font-bold text-sm" x-text="form.nombre"></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Contacto</p>
                                <p class="font-bold text-sm" x-text="form.telefono"></p>
                                <p class="text-xs text-gray-500" x-text="form.correo"></p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <div>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-1" x-text="form.tipo === 'Individual' ? 'Visita Particular' : 'Institución Educativa'"></p>
                                <p class="font-bold text-sm" x-show="form.tipo === 'Institucion'" x-text="form.institucion + ' (' + (form.ciudad === 'lapaz' ? 'La Paz' : 'El Alto') + ')'"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="`${totalPersonas} personas en total`"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total a Pagar</p>
                                <p class="font-black text-2xl text-blue-600 dark:text-blue-400"><span x-text="totalMonto"></span> Bs.</p>
                            </div>
                        </div>
                    </div>

                    {{-- BOTONES FINALES DE CONTROL ACCIONADOS POR ALPINE --}}
                    <div class="flex items-center justify-between">
                        <button type="button" @click="pasoAnterior()" :disabled="enviandoForm"
                            class="px-6 py-3.5 rounded-xl font-bold text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-[#3A3B3C] transition-all flex items-center gap-2 disabled:opacity-50">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i> Modificar Datos
                        </button>
                        <button type="button" @click="enviarReserva()" :disabled="enviandoForm"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-500/30 hover:-translate-y-0.5 px-8 py-3.5 rounded-xl font-black text-sm transition-all flex items-center gap-2 disabled:opacity-50">
                            <span x-show="!enviandoForm" class="flex items-center gap-2">
                                <i data-lucide="check" class="w-5 h-5"></i> Confirmar y Reservar
                            </span>
                            <span x-show="enviandoForm" class="flex items-center gap-2 animate-pulse">
                                Procesando...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lógica de Alpine.js --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reservaStepper', () => ({
                darkMode: false,
                step: 1,
                enviandoForm: false,
                
                // Calendario
                mesActual: new Date().getMonth(),
                anioActual: new Date().getFullYear(),
                diasSemana: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                nombresMeses: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                diasFeriados: ['2026-05-01', '2026-06-21'], // Feriados en formato YYYY-MM-DD
                
                // Datos Reserva
                fechaSel: null,
                sesionSel: null,
                horarios: ['18:00', '19:00', '20:00', '21:00'],
                
                // Formulario
                form: {
                    nombre: '',
                    correo: '',
                    telefono: '',
                    tipo: 'Individual',
                    ciudad: '',
                    institucion: '',
                    particulares: 1,
                    estudiantes: 0,
                    docentes: 0
                },
                
                // Errores de validación
                errores: {
                    nombre: false,
                    correo: false,
                    telefono: false,
                    personas: false,
                    ciudad: false,
                    institucion: false
                },
                
                // Precios
                precios: {
                    particular: 10,
                    estudiante: 5,
                    docente: 10
                },

                // Colegios
                busquedaColegio: '',
                mostrarDropdown: false,
                todosLosColegios: window.listaColegios || ['Colegio San Calixto', 'Colegio Don Bosco', 'Unidad Educativa del Ejército'],
                
                // Computados
                totalMonto: 10,
                totalPersonas: 1,

                init() {
                    setTimeout(() => lucide.createIcons(), 100);
                    
                    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.darkMode = true;
                        document.documentElement.classList.add('dark');
                    }
                    
                    this.calcularTotal();
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.theme = 'dark';
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.theme = 'light';
                    }
                    setTimeout(() => lucide.createIcons(), 50);
                },

                get nombreMesActual() {
                    return this.nombresMeses[this.mesActual];
                },

                get daysInMonth() {
                    return new Date(this.anioActual, this.mesActual + 1, 0).getDate();
                },

                get blankDays() {
                    let firstDay = new Date(this.anioActual, this.mesActual, 1).getDay();
                    return Array.from({ length: firstDay }, (_, i) => i + 1);
                },

                get colegiosFiltrados() {
                    if (this.busquedaColegio === '') return this.todosLosColegios;
                    return this.todosLosColegios.filter(c => c.toLowerCase().includes(this.busquedaColegio.toLowerCase()));
                },

                cambiarMes(val) {
                    this.mesActual += val;
                    if (this.mesActual > 11) {
                        this.mesActual = 0;
                        this.anioActual++;
                    } else if (this.mesActual < 0) {
                        this.mesActual = 11;
                        this.anioActual--;
                    }
                },

                formatFecha(anio, mes, dia) {
                    let m = (mes + 1).toString().padStart(2, '0');
                    let d = dia.toString().padStart(2, '0');
                    return `${anio}-${m}-${d}`;
                },

                obtenerTipoDia(anio, mes, dia) {
                    let fecha = new Date(anio, mes, dia);
                    let strFecha = this.formatFecha(anio, mes, dia);
                    let hoy = new Date();
                    hoy.setHours(0,0,0,0);

                    if (fecha < hoy) return 'pasado';
                    if (this.diasFeriados.includes(strFecha)) return 'feriado';
                    if (fecha.getDay() === 0 || fecha.getDay() === 6) return 'finde';
                    return 'habil';
                },

                manejarClickDia(dia) {
                    let tipo = this.obtenerTipoDia(this.anioActual, this.mesActual, dia);
                    if (tipo === 'habil') {
                        this.fechaSel = this.formatFecha(this.anioActual, this.mesActual, dia);
                        this.sesionSel = null;
                    }
                },

                seleccionarCiudad(ciudad) {
                    this.form.ciudad = ciudad;
                    this.errores.ciudad = false;
                },

                seleccionarColegio(colegio) {
                    this.form.institucion = college = colegio;
                    this.busquedaColegio = colegio;
                    this.mostrarDropdown = false;
                    this.errores.institucion = false;
                },

                sumar(tipo) {
                    this.form[tipo]++;
                    this.calcularTotal();
                },

                restar(tipo) {
                    if (this.form[tipo] > 0) {
                        this.form[tipo]--;
                        this.calcularTotal();
                    }
                },

                calcularTotal() {
                    this.errores.personas = false;
                    if (this.form.tipo === 'Individual') {
                        this.form.estudiantes = 0;
                        this.form.docentes = 0;
                        this.totalPersonas = this.form.particulares;
                        this.totalMonto = this.totalPersonas * this.precios.particular;
                    } else {
                        this.form.particulares = 0;
                        this.totalPersonas = this.form.estudiantes + this.form.docentes;
                        this.totalMonto = (this.form.estudiantes * this.precios.estudiante) + (this.form.docentes * this.precios.docente);
                    }
                },

                validarPaso2() {
                    let valido = true;
                    if (this.form.nombre.trim().length < 3) { this.errores.nombre = true; valido = false; }
                    let regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!regexCorreo.test(this.form.correo)) { this.errores.correo = true; valido = false; }
                    if (this.form.telefono.trim().length < 8) { this.errores.telefono = true; valido = false; }
                    if (this.totalPersonas <= 0) { this.errores.personas = true; valido = false; }
                    if (this.form.tipo === 'Institucion') {
                        if (!this.form.ciudad) { this.errores.ciudad = true; valido = false; }
                        if (!this.form.institucion) { this.errores.institucion = true; valido = false; }
                    }
                    return valido;
                },

                siguientePaso() {
                    if (this.step === 1) {
                        if (this.fechaSel && this.sesionSel) this.step = 2;
                    } else if (this.step === 2) {
                        if (this.validarPaso2()) this.step = 3;
                    }
                    setTimeout(() => lucide.createIcons(), 50);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                pasoAnterior() {
                    if (this.step > 1) this.step--;
                    setTimeout(() => lucide.createIcons(), 50);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                // ENVIAR MEDIANTE FETCH ASÍNCRONO SIN ENTRAR A LA PANTALLA BLANCA DE JSON
                async enviarReserva() {
                    if (this.enviandoForm) return;
                    this.enviandoForm = true;

                    // Formamos la carga de datos estructurada con lo que requiere tu Controlador
                    const datos = {
                        fecha: this.fechaSel,
                        hora_inicio: this.sesionSel, // Mapeado correctamente a "hora_inicio"
                        cantidad_personas: this.totalPersonas,
                        nombre: this.form.nombre,
                        correo: this.form.correo,
                        telefono: this.form.telefono,
                        descripcion: this.form.tipo === 'Institucion' ? `Visita Institucional: ${this.form.institucion} (${this.form.ciudad.toUpperCase()})` : 'Visita Particular'
                    };

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        
                        const respuesta = await fetch('/reservas', { 
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(datos)
                        });

                        const resultado = await respuesta.json();

                        if (respuesta.ok && resultado.success) {
                            // Redirección directa y limpia hacia tu listado de reservas
                            window.location.href = "{{ route('reservas.index') }}";
                        } else {
                            alert('Hubo un error al procesar tu formulario. Revisa los datos.');
                            this.enviandoForm = false;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Error de conexión con el servidor.');
                        this.enviandoForm = false;
                    }
                }
                
            }));
        });
        
    </script>

</x-app-layout>