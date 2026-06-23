<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contacto | Observatorio Max Schreier</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white flex items-center justify-center px-6">
    <main class="max-w-2xl text-center">
        <p class="text-cyan-400 text-xs font-black uppercase tracking-[0.45em] mb-5">Contacto</p>
        <h1 class="text-4xl md:text-6xl font-black mb-6">Observatorio Max Schreier</h1>
        <p class="text-white/70 leading-8">
            Esta sección queda lista para conectar datos institucionales editables. Por ahora puedes volver al sitio principal o revisar la galería.
        </p>
        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-3">
            <a href="{{ route('bienvenido') }}" class="bg-cyan-400 text-slate-950 px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest">Inicio</a>
            <a href="{{ route('galeria') }}" class="bg-white/10 px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest">Galería</a>
        </div>
    </main>
</body>
</html>
