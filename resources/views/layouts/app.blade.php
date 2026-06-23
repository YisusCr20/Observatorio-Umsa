<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatorio - Max Schreier</title>
    @php
        $isEmbedded = request()->boolean('embedded');
        $viteManifestPath = public_path('build/manifest.json');
        $viteManifest = ($isEmbedded && file_exists($viteManifestPath))
            ? json_decode(file_get_contents($viteManifestPath), true)
            : null;
    @endphp
    @if($viteManifest)
        <link rel="stylesheet" href="{{ asset('build/' . $viteManifest['resources/css/app.css']['file']) }}">
        <script type="module" src="{{ asset('build/' . $viteManifest['resources/js/app.js']['file']) }}" defer></script>
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased bg-[#0f172a] text-slate-200">
    @php
        $flatDashboardSurface = request()->is('usuario*', 'admin*', 'secretaria*', 'reservas*', 'profile*');
    @endphp
    @if($isEmbedded)
        <main class="w-full min-h-screen overflow-x-hidden bg-[#F0F2F5] dark:bg-[#18191A]">
            {{ $slot }}
        </main>
    @else
    <div class="relative min-h-screen">
        @unless($flatDashboardSurface)
            <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat" 
                 style="background-image: url('{{ asset('img/ASTRO.jpg') }}'); 
                        opacity: 0.18;">
            </div>

            <div class="fixed inset-0 z-0 bg-gradient-to-b from-[#0f172a]/40 to-[#0f172a] pointer-events-none"></div>
        @endunless

        <div class="relative z-10 flex min-h-screen">
            @include('layouts.navigation')

            <main class="flex-1 overflow-x-hidden">
                {{ $slot }}
            </main>
        </div>
    </div>
    @endif
</body>
</html>
