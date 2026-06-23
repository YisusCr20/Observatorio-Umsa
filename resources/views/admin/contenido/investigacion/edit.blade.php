<div class="admin-card rounded-[28px] p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black uppercase tracking-widest">
                Editar investigación
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Carga investigaciones realizadas, informes, imágenes y enlaces.
            </p>
        </div>

        <a href="{{ route('investigacion') }}" target="_blank"
           class="inline-flex items-center justify-center bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition">
            Ver investigación pública
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[420px_1fr] gap-5">
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4">Nueva investigación</h3>

            <form action="{{ route('admin.contenido.investigacion.items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input name="title" required placeholder="Título de la investigación"
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input name="category" placeholder="Área: Astrofísica, Solar..."
                           class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                    <input type="date" name="event_date"
                           class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                </div>
                <textarea name="body" rows="4" placeholder="Resumen de la investigación"
                          class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white"></textarea>
                <input type="file" name="image" accept="image/*" required
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <input name="button_label" value="Ver informe" placeholder="Texto botón"
                           class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                    <input name="button_url" placeholder="URL del informe"
                           class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                </div>
                <input type="number" name="position" value="1" min="1"
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                    <input type="checkbox" name="is_active" checked>
                    Mostrar investigación
                </label>
                <button class="w-full bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl py-3 font-black uppercase tracking-widest text-xs">
                    Publicar investigación
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse (($researchItems ?? collect()) as $item)
                <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-5">
                    <div>
                        @if ($item->image_path)
                            <img src="{{ asset('storage/' . $item->image_path) }}" class="w-full h-44 object-cover rounded-2xl border border-slate-200 dark:border-slate-700" alt="{{ $item->title }}">
                        @endif
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-[10px] font-black uppercase px-3 py-1 rounded-full {{ $item->is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-500' }}">
                                {{ $item->is_active ? 'Visible' : 'Oculta' }}
                            </span>
                            <span class="text-[10px] font-black uppercase text-slate-400">Orden {{ $item->position }}</span>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('admin.contenido.items.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <input name="title" value="{{ $item->title }}" required class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <input name="category" value="{{ $item->category }}" placeholder="Área" class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                                <input type="date" name="event_date" value="{{ $item->event_date?->format('Y-m-d') }}" class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                                <input type="number" name="position" value="{{ $item->position }}" min="1" class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                            </div>
                            <textarea name="body" rows="4" class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">{{ $item->body }}</textarea>
                            <input type="file" name="image" accept="image/*" class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <input name="button_label" value="{{ $item->button_label }}" placeholder="Texto botón" class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                                <input name="button_url" value="{{ $item->button_url }}" placeholder="URL informe" class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                                <input type="checkbox" name="is_active" @checked($item->is_active)>
                                Mostrar
                            </label>
                            <button class="bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 font-black uppercase tracking-widest text-xs">Actualizar</button>
                        </form>

                        <form action="{{ route('admin.contenido.items.destroy', $item) }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('¿Eliminar esta investigación?')" class="bg-rose-600 hover:bg-rose-500 text-white rounded-2xl px-5 py-3 font-black uppercase tracking-widest text-xs">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-500 px-4 py-3 text-sm font-bold">
                    Todavía no hay investigaciones cargadas.
                </div>
            @endforelse
        </div>
    </div>
</div>
