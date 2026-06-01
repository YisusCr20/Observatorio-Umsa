<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Observatorio - Max Schreier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="font-sans antialiased bg-[#0f172a] text-slate-200">
    <div class="relative min-h-screen">
        
        <div class="fixed inset-0 z-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('{{ asset('img/ASTRO.jpg') }}'); 
                    opacity: 0.18;">
        </div>

        <div class="fixed inset-0 z-0 bg-gradient-to-b from-[#0f172a]/40 to-[#0f172a] pointer-events-none"></div>

        <div class="relative z-10 flex min-h-screen">
            @include('layouts.navigation')

            <main class="flex-1 overflow-x-hidden">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>