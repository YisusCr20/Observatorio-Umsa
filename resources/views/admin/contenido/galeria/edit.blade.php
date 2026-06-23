<div class="admin-card rounded-[28px] p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black uppercase tracking-widest">
                Editar galería de imágenes
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Agrega, ordena y elimina fotografías visibles en la galería pública.
            </p>
        </div>

        <a href="{{ route('galeria') }}" target="_blank"
           class="inline-flex items-center justify-center bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition">
            Ver galería pública
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[420px_1fr] gap-5">
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4">
                Nueva imagen
            </h3>

            <form action="{{ route('admin.contenido.galeria.images.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <input type="text" name="title" placeholder="Título de la imagen"
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">

                <textarea name="description" rows="3" placeholder="Descripción breve"
                          class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white"></textarea>

                <input type="file" name="image" accept="image/*" required
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">

                <input type="number" name="position" value="1" min="1"
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                    <input type="checkbox" name="is_active" checked>
                    Mostrar imagen
                </label>

                <button type="submit"
                        class="w-full bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl py-3 font-black uppercase tracking-widest text-xs">
                    Agregar imagen
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4">
                Imágenes existentes
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse (($galleryImages ?? collect()) as $image)
                    <div class="rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden bg-slate-50 dark:bg-[#07111f]">
                        <img src="{{ asset('storage/' . $image->image_path) }}"
                             class="w-full h-48 object-cover"
                             alt="{{ $image->title }}">

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h4 class="text-sm font-black">
                                        {{ $image->title ?? 'Sin título' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Orden: {{ $image->position }}
                                    </p>
                                </div>

                                <span class="text-[9px] font-black px-2.5 py-1.5 rounded-xl {{ $image->is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-500' }} uppercase">
                                    {{ $image->is_active ? 'Visible' : 'Oculta' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500 mt-3 line-clamp-2">
                                {{ $image->description }}
                            </p>

                            <form action="{{ route('admin.contenido.galeria.images.destroy', $image) }}" method="POST" class="mt-4">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        onclick="return confirm('¿Eliminar esta imagen?')"
                                        class="bg-rose-600 hover:bg-rose-500 text-white rounded-2xl px-4 py-2 font-black uppercase tracking-widest text-[10px]">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-500 px-4 py-3 text-sm font-bold">
                        Todavía no hay imágenes en galería.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
