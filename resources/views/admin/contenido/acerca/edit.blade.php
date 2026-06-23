@php
    $aboutSections = $aboutSections ?? collect();

    $defaultSections = [
        'hero' => [
            'title' => 'El Observatorio',
            'subtitle' => 'Exploración e investigación científica UMSA',
            'body' => 'Un espacio universitario dedicado a la observación astronómica, la formación académica y la divulgación científica en La Paz.',
            'position' => 1,
        ],
        'historia' => [
            'title' => 'Antecedentes institucionales',
            'subtitle' => 'Historia y legado',
            'body' => 'Aquí se detallará la trayectoria del Observatorio Astronómico Max Schreier y su vínculo con la Universidad Mayor de San Andrés.',
            'position' => 2,
        ],
        'investigacion' => [
            'title' => 'Líneas de investigación',
            'subtitle' => 'Ciencia, monitoreo y formación',
            'body' => 'Investigación aplicada en astrofísica, monitoreo solar, estudios atmosféricos y actividades académicas con docentes y estudiantes.',
            'position' => 3,
        ],
        'ubicacion' => [
            'title' => 'Ubicación',
            'subtitle' => 'Cota Cota, La Paz',
            'body' => 'El observatorio se encuentra en la zona de Cota Cota, Calle 27, como parte del entorno académico de la UMSA.',
            'position' => 4,
        ],
    ];
@endphp

<div class="admin-card rounded-[28px] p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black uppercase tracking-widest">
                Editar página Acerca de
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Cambia títulos, textos, orden e imágenes institucionales.
            </p>
        </div>

        <a href="{{ route('acerca') }}" target="_blank"
           class="inline-flex items-center justify-center bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition">
            Ver Acerca público
        </a>
    </div>

    <form action="{{ route('admin.contenido.acerca.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        @foreach ($defaultSections as $key => $fallback)
            @php
                $section = $aboutSections->get($key);
            @endphp

            <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
                <div class="flex flex-col lg:flex-row gap-5">
                    <div class="lg:w-56 shrink-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-blue-600 dark:text-blue-400 mb-3">
                            {{ ucfirst($key) }}
                        </p>

                        <div class="h-36 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-[#07111f]">
                            @if ($section?->image_path)
                                <img src="{{ asset('storage/' . $section->image_path) }}"
                                     class="w-full h-full object-cover"
                                     alt="{{ $section->title }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-bold text-center px-4">
                                    Sin imagen cargada
                                </div>
                            @endif
                        </div>

                        <label class="mt-3 flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                            <input type="checkbox" name="sections[{{ $key }}][is_active]" @checked($section?->is_active ?? true)>
                            Visible
                        </label>
                    </div>

                    <div class="flex-1 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="text"
                                   name="sections[{{ $key }}][title]"
                                   value="{{ old("sections.$key.title", $section?->title ?? $fallback['title']) }}"
                                   placeholder="Título"
                                   class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">

                            <input type="text"
                                   name="sections[{{ $key }}][subtitle]"
                                   value="{{ old("sections.$key.subtitle", $section?->subtitle ?? $fallback['subtitle']) }}"
                                   placeholder="Subtítulo"
                                   class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                        </div>

                        <textarea name="sections[{{ $key }}][body]"
                                  rows="4"
                                  placeholder="Texto de la sección"
                                  class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">{{ old("sections.$key.body", $section?->body ?? $fallback['body']) }}</textarea>

                        <div class="grid grid-cols-1 md:grid-cols-[1fr_140px] gap-4">
                            <input type="file"
                                   name="sections[{{ $key }}][image]"
                                   accept="image/*"
                                   class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">

                            <input type="number"
                                   name="sections[{{ $key }}][position]"
                                   value="{{ old("sections.$key.position", $section?->position ?? $fallback['position']) }}"
                                   min="1"
                                   class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white"
                                   title="Orden">
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white rounded-2xl py-3 font-black uppercase tracking-widest text-xs">
            Guardar página Acerca de
        </button>
    </form>
</div>
