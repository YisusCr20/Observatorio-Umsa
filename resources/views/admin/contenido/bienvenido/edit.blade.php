<div class="admin-card rounded-[28px] p-5">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-black uppercase tracking-widest">
                Editar página Bienvenido
            </h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Cambia fondos, imágenes principales y slides del inicio público.
            </p>
        </div>

        <a href="{{ route('bienvenido') }}" target="_blank"
           class="inline-flex items-center justify-center bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-3 text-xs font-black uppercase tracking-widest hover:scale-[1.02] transition">
            Ver portada pública
        </a>
    </div>

    @if (session('status'))
        <div class="mb-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-500 px-4 py-3 text-sm font-bold">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 px-4 py-3 text-sm font-bold">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- FONDOS --}}
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4">
                Fondos principales
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">
                        Fondo oscuro actual
                    </p>

                    <div class="h-36 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-[#07111f]">
                        @if (!empty($settings['background_dark']))
                            <img src="{{ asset('storage/' . $settings['background_dark']) }}"
                                 class="w-full h-full object-cover"
                                 alt="Fondo oscuro">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-bold">
                                Sin fondo cargado
                            </div>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">
                        Fondo claro actual
                    </p>

                    <div class="h-36 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-[#07111f]">
                        @if (!empty($settings['background_light']))
                            <img src="{{ asset('storage/' . $settings['background_light']) }}"
                                 class="w-full h-full object-cover"
                                 alt="Fondo claro">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-bold">
                                Sin fondo cargado
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.contenido.bienvenido.fondos') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">
                        Nuevo fondo modo oscuro
                    </label>
                    <input type="file" name="background_dark" accept="image/*"
                           class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">
                        Nuevo fondo modo claro
                    </label>
                    <input type="file" name="background_light" accept="image/*"
                           class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white rounded-2xl py-3 font-black uppercase tracking-widest text-xs">
                    Guardar fondos
                </button>
            </form>
        </div>

        {{-- NUEVO SLIDE --}}
        <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5">
            <h3 class="text-sm font-black uppercase tracking-widest mb-4">
                Nuevo slide de portada
            </h3>

            <form action="{{ route('admin.contenido.bienvenido.slides.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" name="title_highlight" placeholder="Texto resaltado: OBSERVA" required
                           class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">

                    <input type="text" name="title_normal" placeholder="Texto normal: EL CIELO" required
                           class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                </div>

                <textarea name="description" placeholder="Descripción del slide"
                          class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white"></textarea>

                <input type="file" name="image" accept="image/*" required
                       class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <select name="image_shape"
                            class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                        <option value="rounded">Rectangular redondeada</option>
                        <option value="circle">Circular</option>
                        <option value="tilted">Inclinada</option>
                    </select>

                    <input type="number" name="position" value="1" min="1"
                           class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                    <input type="checkbox" name="is_active" checked>
                    Mostrar slide en la portada
                </label>

                <button type="submit"
                        class="w-full bg-cyan-600 hover:bg-cyan-500 text-white rounded-2xl py-3 font-black uppercase tracking-widest text-xs">
                    Crear slide
                </button>
            </form>
        </div>
    </div>

    {{-- SLIDES EXISTENTES --}}
    <div class="mt-6">
        <h3 class="text-sm font-black uppercase tracking-widest mb-4">
            Slides existentes
        </h3>

        <div class="space-y-4">
            @forelse ($slides ?? [] as $slide)
                <div class="bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-700 rounded-3xl p-5 grid grid-cols-1 xl:grid-cols-[220px_1fr] gap-5">

                    <div>
                        @if ($slide->image_path)
                            <img src="{{ asset('storage/' . $slide->image_path) }}"
                                 class="w-full h-44 object-cover rounded-2xl border border-slate-200 dark:border-slate-700"
                                 alt="{{ $slide->title_highlight }} {{ $slide->title_normal }}">
                        @else
                            <div class="w-full h-44 rounded-2xl bg-slate-100 dark:bg-[#07111f] border border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center text-xs text-slate-400 font-bold">
                                Sin imagen
                            </div>
                        @endif

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full
                                {{ $slide->is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-500' }}">
                                {{ $slide->is_active ? 'Activo' : 'Inactivo' }}
                            </span>

                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                                Orden: {{ $slide->position }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <form action="{{ route('admin.contenido.bienvenido.slides.update', $slide) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <input type="text" name="title_highlight" value="{{ $slide->title_highlight }}" required
                                       class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">

                                <input type="text" name="title_normal" value="{{ $slide->title_normal }}" required
                                       class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                            </div>

                            <textarea name="description"
                                      class="w-full bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">{{ $slide->description }}</textarea>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <input type="file" name="image" accept="image/*"
                                       class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm">

                                <select name="image_shape"
                                        class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                                    <option value="rounded" @selected($slide->image_shape === 'rounded')>Rectangular</option>
                                    <option value="circle" @selected($slide->image_shape === 'circle')>Circular</option>
                                    <option value="tilted" @selected($slide->image_shape === 'tilted')>Inclinada</option>
                                </select>

                                <input type="number" name="position" value="{{ $slide->position }}" min="1"
                                       class="bg-slate-50 dark:bg-[#07111f] border border-slate-200 dark:border-slate-700 rounded-2xl p-3 text-sm dark:text-white">
                            </div>

                            <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300 font-bold">
                                <input type="checkbox" name="is_active" @checked($slide->is_active)>
                                Mostrar en la portada
                            </label>

                            <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-500 text-white rounded-2xl px-5 py-3 font-black uppercase tracking-widest text-xs">
                                Actualizar slide
                            </button>
                        </form>

                        <form action="{{ route('admin.contenido.bienvenido.slides.destroy', $slide) }}" method="POST" class="mt-3">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    onclick="return confirm('¿Eliminar este slide?')"
                                    class="bg-rose-600 hover:bg-rose-500 text-white rounded-2xl px-5 py-3 font-black uppercase tracking-widest text-xs">
                                Eliminar slide
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-500 px-4 py-3 text-sm font-bold">
                    Todavía no tienes slides registrados. Crea uno para que aparezca en la portada.
                </div>
            @endforelse
        </div>
    </div>
</div>