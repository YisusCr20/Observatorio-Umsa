@php
    $settings = $settings ?? collect();
    $slides = $slides ?? collect();

    $bgDarkPath = $settings['background_dark'] ?? null;
    $bgLightPath = $settings['background_light'] ?? null;

    $bgDark = $bgDarkPath
        ? asset('storage/' . $bgDarkPath)
        : asset('img/ASTRO.jpg');

    $bgLight = $bgLightPath
        ? asset('storage/' . $bgLightPath)
        : asset('img/FONDO.jpg');

    $firstSlide = $slides->first();
    $firstSlideImage = $firstSlide?->image_path ? asset('storage/' . $firstSlide->image_path) : asset('img/Slide1.jpg');
@endphp

<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Observatorio UMSA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ $firstSlideImage }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
    @vite(['resources/css/app.css'])

    <style>
        .glass {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .light .glass {
            background: rgba(255, 255, 255, 0.76);
            border: 1px solid rgba(15, 23, 42, 0.10);
        }

        body {
            transition: background 0.8s, color 0.5s;
        }

        .text-gradient {
            background: linear-gradient(135deg, #fff, #3b82f6, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .light .text-gradient {
            background: linear-gradient(135deg, #0f172a, #3b82f6, #7c3aed);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .float-anim {
            animation: floating 4s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% {
                transform: translateY(0px) scale(1);
            }

            50% {
                transform: translateY(-20px) scale(1.02);
            }
        }

        .slide {
            display: none;
            opacity: 0;
            transition: opacity 1s ease-in-out, transform 1s ease;
            transform: scale(0.95);
        }

        .slide.active {
            display: flex;
            opacity: 1;
            transform: scale(1);
        }

        .bot-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            animation: pulse-blue 2s infinite;
        }

        @keyframes pulse-blue {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .social-btn {
            @apply w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full glass transition-all duration-300 text-white hover:bg-cosmos-primary hover:scale-110 shadow-lg;
        }

        .nav-scrolled {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            background: rgba(2, 4, 10, 0.82);
            border-color: transparent;
        }

        .light .nav-scrolled {
            background: rgba(255, 255, 255, 0.92);
        }

        .light .public-link span,
        .light .brand-title,
        .light .mobile-icon {
            color: #0f172a !important;
        }

        .light .hero-text {
            color: rgba(15, 23, 42, 0.72) !important;
        }

        .chatbot-scroll {
            scrollbar-width: thin;
            scrollbar-color: rgba(59, 130, 246, 0.55) transparent;
        }

        .chatbot-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .chatbot-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .chatbot-scroll::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.55);
            border-radius: 999px;
        }

        #particles-js {
            background-image:
                radial-gradient(circle at 12% 24%, rgba(59, 130, 246, 0.30) 0 1px, transparent 2px),
                radial-gradient(circle at 75% 18%, rgba(34, 211, 238, 0.26) 0 1px, transparent 2px),
                radial-gradient(circle at 54% 72%, rgba(255, 255, 255, 0.18) 0 1px, transparent 2px);
            background-size: 180px 180px, 260px 260px, 220px 220px;
        }

        @media (max-width: 767px) {
            .float-anim,
            .bot-glow {
                animation: none;
            }

            #particles-js {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-cosmos-950 text-white font-sans dark overflow-x-hidden">

    <div id="particles-js" class="fixed inset-0 z-10 pointer-events-none"></div>

    <!-- FONDO DINÁMICO -->
    <div class="fixed inset-0 z-0">
        <div id="slide-dark"
             class="absolute inset-0 bg-cover bg-center opacity-100 transition duration-1000 scale-105"
             style="background-image:url('{{ $bgDark }}');">
        </div>

        <div id="slide-light"
             class="absolute inset-0 bg-cover bg-center opacity-0 transition duration-1000 scale-105"
             data-bg="{{ $bgLight }}">
        </div>

        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-cosmos-950/55 to-cosmos-950 dark:to-cosmos-950/95"></div>
    </div>

    <!-- REDES SOCIALES -->
    <div class="fixed left-4 bottom-4 md:left-8 md:bottom-8 z-40 flex md:flex-col gap-3 items-center">
        <a href="#" class="social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="#" class="social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
    </div>

    <!-- NAVEGACIÓN -->
    <header class="fixed top-0 w-full z-50 p-4 md:p-6 transition-all duration-500" id="navbar">
        <nav class="max-w-7xl mx-auto glass rounded-2xl px-5 md:px-8 py-3 md:py-4 flex justify-between items-center shadow-2xl transition-all duration-500" id="nav-container">

            <!-- Logo -->
            <div class="flex flex-col relative z-50 cursor-pointer hover:scale-105 transition-transform" onclick="window.scrollTo(0,0)">
                <span class="brand-title font-cinzel font-black tracking-widest text-base md:text-xl text-white drop-shadow-md">
                    MAX SCHREIER
                </span>
                <span class="text-[7px] md:text-[9px] uppercase tracking-[0.4em] text-cosmos-neon font-bold">
                    Observatorio • UMSA
                </span>
            </div>

            <!-- Botones móvil -->
            <div class="flex items-center gap-4 lg:hidden relative z-50">
                <button onclick="toggleTheme()" class="w-10 h-5 bg-white/10 rounded-full flex items-center px-1 transition-all border border-white/20">
                    <div id="toggle-circle-mobile" class="w-3 h-3 bg-white rounded-full transition-transform duration-500"></div>
                </button>

                <button id="mobile-menu-btn" class="mobile-icon text-white text-2xl focus:outline-none hover:text-cosmos-neon transition-colors">
                    <i class="fas fa-bars-staggered"></i>
                </button>
            </div>

            <!-- Enlaces desktop -->
            <div class="hidden lg:flex items-center gap-8 text-[11px] font-bold uppercase tracking-[0.2em]">
                <a href="{{ route('acerca') }}" class="public-link relative group py-2">
                    <span class="text-white/80 group-hover:text-white transition-colors">Acerca de</span>
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
                </a>

                <a href="{{ route('investigacion') }}" class="public-link relative group py-2">
                    <span class="text-white/80 group-hover:text-white transition-colors">Investigación</span>
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
                </a>

                <a href="{{ route('eventos') }}" class="public-link relative group py-2">
                    <span class="text-white/80 group-hover:text-white transition-colors">Eventos</span>
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
                </a>

                <a href="{{ route('galeria') }}" class="public-link relative group py-2">
                    <span class="text-white/80 group-hover:text-white transition-colors">Galería</span>
                    <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
                </a>

                <div class="w-[1px] h-6 bg-white/20 mx-2"></div>

                <button onclick="toggleTheme()" class="w-12 h-6 bg-white/10 rounded-full flex items-center px-1 transition-all border border-white/20 hover:bg-white/20">
                    <div id="toggle-circle" class="w-4 h-4 bg-white rounded-full transition-transform duration-500 shadow-md"></div>
                </button>

                <div class="flex items-center gap-3 ml-2">
                    <a href="{{ route('login') }}" class="bg-transparent border border-white/20 px-6 py-2.5 rounded-full hover:bg-white/10 hover:border-white/50 transition-all text-white backdrop-blur-sm">
                        Iniciar Sesion
                    </a>

                    <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-cosmos-primary px-7 py-2.5 rounded-full text-white hover:scale-105 hover:shadow-[0_0_20px_rgba(59,130,246,0.6)] transition-all duration-300">
                        Registrarse
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- MENÚ MÓVIL -->
    <div id="mobile-menu" class="fixed inset-0 bg-cosmos-950/98 backdrop-blur-2xl z-40 transform translate-x-full transition-transform duration-500 lg:hidden flex flex-col justify-center items-center">
        <div class="flex flex-col items-center gap-8 text-lg font-cinzel font-bold uppercase tracking-widest w-full px-8">
            <a href="{{ route('acerca') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Acerca de</a>
            <a href="{{ route('investigacion') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Investigación</a>
            <a href="{{ route('eventos') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Eventos</a>
            <a href="{{ route('galeria') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 mb-4">Galería</a>

            <div class="flex flex-col gap-4 w-full max-w-xs mt-4">
                <a href="{{ route('login') }}" class="glass w-full py-4 rounded-xl text-center text-white text-sm tracking-[0.2em] hover:bg-white/10 transition-colors">
                    INICIAR SESIÓN
                </a>

                <a href="{{ route('register') }}" class="bg-cosmos-primary w-full py-4 rounded-xl text-center text-white text-sm tracking-[0.2em] shadow-lg shadow-blue-500/30">
                    CREAR CUENTA
                </a>
            </div>
        </div>
    </div>

    <!-- HERO SLIDER -->
    <main class="relative z-20 min-h-[100dvh] max-w-7xl mx-auto flex items-center justify-center px-6 pt-24 md:pt-20">

        @forelse ($slides as $slide)
            @php
                $shapeClass = match ($slide->image_shape) {
                    'circle' => 'rounded-full border-2 border-purple-500/30',
                    'tilted' => 'rounded-[2rem] rotate-3',
                    default => 'rounded-3xl border border-white/10',
                };
            @endphp

            <div class="slide {{ $loop->first ? 'active' : '' }} flex-col-reverse md:flex-row items-center justify-center gap-8 md:gap-12 w-full min-h-[calc(100dvh-6rem)] pb-24 md:pb-0">
                <div class="flex-1 text-center md:text-left flex flex-col items-center md:items-start">
                    <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-cinzel font-black leading-tight drop-shadow-2xl">
                        <span class="text-gradient italic">{{ $slide->title_highlight }}</span><br>
                        {{ $slide->title_normal }}
                    </h1>

                    <p class="hero-text mt-4 md:mt-6 text-white/70 text-sm md:text-lg max-w-lg leading-relaxed font-light">
                        {{ $slide->description }}
                    </p>
                </div>

                <div class="flex-1 flex justify-center items-center w-full">
                    <img src="{{ asset('storage/' . $slide->image_path) }}"
                         width="450"
                         height="450"
                         loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                         decoding="async"
                         fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                         sizes="(max-width: 640px) 16rem, (max-width: 768px) 20rem, 450px"
                         class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] object-cover {{ $shapeClass }} float-anim drop-shadow-[0_0_30px_rgba(59,130,246,0.3)]"
                         alt="{{ $slide->title_highlight }} {{ $slide->title_normal }}">
                </div>
            </div>
        @empty
            <div class="slide active flex-col-reverse md:flex-row items-center justify-center gap-8 md:gap-12 w-full min-h-[calc(100dvh-6rem)] pb-24 md:pb-0">
                <div class="flex-1 text-center md:text-left flex flex-col items-center md:items-start">
                    <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-cinzel font-black leading-tight drop-shadow-2xl">
                        <span class="text-gradient italic">OBSERVA</span><br>
                        EL CIELO
                    </h1>

                    <p class="hero-text mt-4 md:mt-6 text-white/70 text-sm md:text-lg max-w-lg leading-relaxed font-light">
                        Descubre los secretos del cosmos con tecnología de vanguardia en el corazón de La Paz.
                    </p>
                </div>

                <div class="flex-1 flex justify-center items-center w-full">
                    <img src="{{ asset('img/Slide1.jpg') }}"
                         width="450"
                         height="450"
                         loading="eager"
                         decoding="async"
                         fetchpriority="high"
                         sizes="(max-width: 640px) 16rem, (max-width: 768px) 20rem, 450px"
                         class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] object-cover rounded-3xl float-anim drop-shadow-[0_0_30px_rgba(59,130,246,0.3)] border border-white/10"
                         alt="Observatorio Astronómico Max Schreier">
                </div>
            </div>
        @endforelse

        <!-- Indicadores -->
        <div class="absolute bottom-8 md:bottom-12 left-1/2 -translate-x-1/2 flex gap-4" id="dot-container"></div>
    </main>

    <!-- CHATBOT DE RESERVAS -->
    <div id="chatbot-widget" class="fixed bottom-3 right-3 left-3 sm:left-auto sm:bottom-4 sm:right-4 md:bottom-8 md:right-8 z-50">
        <section id="chatbot-panel"
                 class="hidden flex mb-3 sm:mb-4 w-full sm:w-[calc(100vw-2rem)] sm:max-w-[420px] h-[min(560px,calc(100svh-6rem))] sm:h-[min(620px,calc(100vh-7rem))] rounded-[1.35rem] sm:rounded-[1.75rem] overflow-hidden bg-white/95 dark:bg-[#080d18]/95 shadow-2xl border border-white/15 backdrop-blur-2xl flex-col">
            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 text-white px-4 sm:px-5 py-3 sm:py-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[10px] font-black uppercase tracking-[0.28em] opacity-80">Asistente virtual</p>
                    <h2 class="text-base sm:text-lg font-black leading-tight truncate">Reservas Max Schreier</h2>
                    <p class="text-[11px] text-white/80 font-semibold mt-0.5">Horarios, costos y cupos</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div id="chatbot-language" class="flex rounded-full bg-white/15 p-1 text-[10px] font-black">
                        <button type="button" data-lang="es" class="chat-lang px-2 py-1 rounded-full">ES</button>
                        <button type="button" data-lang="ay" class="chat-lang px-2 py-1 rounded-full">AY</button>
                        <button type="button" data-lang="en" class="chat-lang px-2 py-1 rounded-full">EN</button>
                    </div>
                    <button type="button" onclick="toggleChat()" class="w-9 h-9 rounded-full bg-white/15 hover:bg-white/25 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div id="chatbot-messages" class="chatbot-scroll flex-1 min-h-0 overflow-y-auto px-3 sm:px-4 py-4 space-y-3 bg-slate-50 dark:bg-[#080d18] text-slate-900 dark:text-white">
                <div class="flex items-start gap-2">
                    <div class="w-8 h-8 rounded-full bg-cosmos-primary text-white flex items-center justify-center text-xs font-black shrink-0">MS</div>
                    <div class="rounded-2xl rounded-tl-sm bg-white dark:bg-white/10 shadow-sm border border-slate-200 dark:border-white/10 px-4 py-3 text-sm leading-6">
                        Hola. Soy el asistente de reservas y puedo ayudarte con horarios, cupos, fechas y costos.<br>
                        Kamisaki. Reservas tuqita yanapt'asmawa: horas, cupos, urunaka ukhamaraki qullqi.<br>
                        Hi. I can help you with reservations, schedules, availability, dates and prices.
                    </div>
                </div>
            </div>

            <div class="shrink-0 bg-white dark:bg-[#080d18] px-3 sm:px-4 py-3 border-t border-slate-200 dark:border-white/10">
                <div id="chatbot-suggestions" class="grid grid-cols-2 gap-2 pb-3">
                    <button type="button" class="chat-suggestion rounded-xl bg-blue-50 dark:bg-white/10 text-blue-700 dark:text-cyan-200 px-3 py-2 text-[11px] font-black text-left leading-tight hover:bg-blue-100 dark:hover:bg-white/15 transition" data-question="¿Qué horarios hay?">Horarios</button>
                    <button type="button" class="chat-suggestion rounded-xl bg-blue-50 dark:bg-white/10 text-blue-700 dark:text-cyan-200 px-3 py-2 text-[11px] font-black text-left leading-tight hover:bg-blue-100 dark:hover:bg-white/15 transition" data-question="¿Hay cupos mañana?">Cupos mañana</button>
                    <button type="button" class="chat-suggestion rounded-xl bg-blue-50 dark:bg-white/10 text-blue-700 dark:text-cyan-200 px-3 py-2 text-[11px] font-black text-left leading-tight hover:bg-blue-100 dark:hover:bg-white/15 transition" data-question="¿Cómo hago una reserva?">Reservar</button>
                    <button type="button" class="chat-suggestion rounded-xl bg-blue-50 dark:bg-white/10 text-blue-700 dark:text-cyan-200 px-3 py-2 text-[11px] font-black text-left leading-tight hover:bg-blue-100 dark:hover:bg-white/15 transition" data-question="¿Cuál es el costo?">Costos</button>
                </div>

                <form id="chatbot-form" class="flex items-center gap-2">
                    <input id="chatbot-input"
                           type="text"
                           autocomplete="off"
                           maxlength="600"
                           placeholder="Escribe tu pregunta..."
                           class="min-w-0 flex-1 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/10 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-blue-500 dark:text-white">
                    <button type="submit"
                            class="w-12 h-12 rounded-2xl bg-cosmos-primary text-white flex items-center justify-center hover:scale-105 transition">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </section>

        <button id="chatbot-toggle" type="button" onclick="toggleChat()" class="bot-glow ml-auto w-14 h-14 md:w-16 md:h-16 bg-cosmos-primary rounded-full flex items-center justify-center transition-transform hover:scale-110 active:scale-95 group shadow-2xl">
            <svg class="w-7 h-7 md:w-8 md:h-8 text-white transition-all group-hover:rotate-12 group-hover:scale-110" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C7.58 2 4 5.58 4 10V12C2.9 12 2 12.9 2 14V16C2 17.1 2.9 18 4 18V19C4 20.66 5.34 22 7 22H17C18.66 22 20 20.66 20 19V18C21.1 18 22 17.1 22 16V14C22 12.9 21.1 12 20 12V10C20 5.58 16.42 2 12 2Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"/>
                <rect x="7" y="10" width="10" height="6" rx="2" stroke="currentColor" stroke-width="1.5" fill="currentColor" fill-opacity="0.3"/>
                <circle cx="9.5" cy="13" r="1" fill="currentColor"/>
                <circle cx="14.5" cy="13" r="1" fill="currentColor"/>
                <path d="M11 7H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dotContainer = document.getElementById('dot-container');

        if (slides.length > 0 && dotContainer) {
            slides.forEach((_, i) => {
                const dot = document.createElement('button');
                dot.className = `dot w-10 h-2 md:w-12 md:h-2 rounded-full transition-all duration-500 ${i === 0 ? 'bg-cosmos-primary shadow-[0_0_10px_#3b82f6]' : 'bg-white/20 hover:bg-white/40'}`;
                dot.onclick = () => setSlide(i);
                dotContainer.appendChild(dot);
            });
        }

        const dots = document.querySelectorAll('.dot');

        function setSlide(index) {
            if (!slides.length || !dots.length) return;

            slides[currentSlide].classList.remove('active');
            dots[currentSlide].classList.remove('bg-cosmos-primary', 'shadow-[0_0_10px_#3b82f6]');
            dots[currentSlide].classList.add('bg-white/20');

            currentSlide = index;

            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.remove('bg-white/20');
            dots[currentSlide].classList.add('bg-cosmos-primary', 'shadow-[0_0_10px_#3b82f6]');
        }

        if (slides.length > 1) {
            setInterval(() => {
                if (document.hidden) return;
                setSlide((currentSlide + 1) % slides.length);
            }, 7000);
        }

        function applyTheme() {
            let isDark = document.body.classList.contains('dark');
            let darkBg = document.getElementById('slide-dark');
            let lightBg = document.getElementById('slide-light');
            let circles = [
                document.getElementById('toggle-circle'),
                document.getElementById('toggle-circle-mobile')
            ];

            if (isDark) {
                if (darkBg && lightBg) {
                    darkBg.style.opacity = 1;
                    lightBg.style.opacity = 0;
                }

                circles.forEach(c => {
                    if (c) c.style.transform = 'translateX(0)';
                });

            } else {
                if (darkBg && lightBg) {
                    darkBg.style.opacity = 0;
                    if (!lightBg.style.backgroundImage) {
                        lightBg.style.backgroundImage = `url('${lightBg.dataset.bg}')`;
                    }
                    lightBg.style.opacity = 1;
                }

                circles.forEach(c => {
                    if (c) c.style.transform = 'translateX(100%)';
                });

            }
        }

        function toggleTheme() {
            document.body.classList.toggle('dark');
            document.body.classList.toggle('light');

            localStorage.setItem(
                'public-theme',
                document.body.classList.contains('dark') ? 'dark' : 'light'
            );

            applyTheme();
        }

        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        let menuOpen = false;

        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => {
                menuOpen = !menuOpen;

                if (menuOpen) {
                    mobileMenu.classList.remove('translate-x-full');
                    mobileBtn.innerHTML = '<i class="fas fa-times"></i>';
                    document.body.style.overflow = 'hidden';
                } else {
                    mobileMenu.classList.add('translate-x-full');
                    mobileBtn.innerHTML = '<i class="fas fa-bars-staggered"></i>';
                    document.body.style.overflow = 'auto';
                }
            });
        }

        window.addEventListener('scroll', () => {
            const navContainer = document.getElementById('nav-container');

            if (navContainer) {
                if (window.scrollY > 50) {
                    navContainer.classList.add('nav-scrolled');
                } else {
                    navContainer.classList.remove('nav-scrolled');
                }
            }
        });

        const chatbotPanel = document.getElementById('chatbot-panel');
        const chatbotMessages = document.getElementById('chatbot-messages');
        const chatbotForm = document.getElementById('chatbot-form');
        const chatbotInput = document.getElementById('chatbot-input');
        const chatbotSuggestions = document.getElementById('chatbot-suggestions');
        const chatbotLanguage = document.getElementById('chatbot-language');
        const chatbotLanguageTexts = {
            es: {
                placeholder: 'Escribe tu pregunta...',
                loading: 'Estoy revisando la información de reservas...',
                suggestions: ['¿Qué horarios hay?', '¿Hay cupos mañana?', '¿Cómo hago una reserva?', '¿Cuál es el costo?'],
            },
            ay: {
                placeholder: 'Jiskt’awima qillqt’ama...',
                loading: 'Reservas yatiyawipa uñakipaskta...',
                suggestions: ['Kuna horas utji?', 'Qharuru cupos utjiti?', 'Kunjamsa reserva lurta?', 'Qawqha qullqisa?'],
            },
            en: {
                placeholder: 'Type your question...',
                loading: 'Checking reservation information...',
                suggestions: ['What hours are available?', 'Is tomorrow available?', 'How do I book?', 'How much does it cost?'],
            },
        };
        let currentChatLanguage = localStorage.getItem('chatbot-language') || 'es';
        let chatbotBusy = false;

        function toggleChat() {
            if (!chatbotPanel) return;

            chatbotPanel.classList.toggle('hidden');

            if (!chatbotPanel.classList.contains('hidden') && chatbotInput) {
                setTimeout(() => chatbotInput.focus(), 150);
            }
        }

        function appendChatMessage(text, sender = 'bot') {
            if (!chatbotMessages) return;

            const isUser = sender === 'user';
            const wrapper = document.createElement('div');
            wrapper.className = `flex items-start gap-2 ${isUser ? 'justify-end' : ''}`;

            const safeText = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\n/g, '<br>');

            wrapper.innerHTML = isUser
                ? `<div class="rounded-2xl rounded-tr-sm bg-blue-600 text-white px-4 py-3 text-sm leading-6 max-w-[82%] shadow-md shadow-blue-500/20">${safeText}</div>`
                : `<div class="w-8 h-8 rounded-full bg-cosmos-primary text-white flex items-center justify-center text-xs font-black shrink-0">MS</div><div class="rounded-2xl rounded-tl-sm bg-white dark:bg-white/10 border border-slate-200 dark:border-white/10 shadow-sm px-4 py-3 text-sm leading-6 max-w-[82%]">${safeText}</div>`;

            chatbotMessages.appendChild(wrapper);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;

            return wrapper;
        }

        function renderChatSuggestions(suggestions = []) {
            if (!chatbotSuggestions || !suggestions.length) return;

            chatbotSuggestions.innerHTML = suggestions.map(suggestion => `
                <button type="button"
                        class="chat-suggestion rounded-xl bg-blue-50 dark:bg-white/10 text-blue-700 dark:text-cyan-200 px-3 py-2 text-[11px] font-black text-left leading-tight hover:bg-blue-100 dark:hover:bg-white/15 transition"
                        data-question="${suggestion.replace(/"/g, '&quot;')}">
                    ${suggestion}
                </button>
            `).join('');
        }

        function applyChatLanguage(language) {
            currentChatLanguage = chatbotLanguageTexts[language] ? language : 'es';
            localStorage.setItem('chatbot-language', currentChatLanguage);

            if (chatbotInput) {
                chatbotInput.placeholder = chatbotLanguageTexts[currentChatLanguage].placeholder;
            }

            if (chatbotLanguage) {
                chatbotLanguage.querySelectorAll('.chat-lang').forEach((button) => {
                    const active = button.dataset.lang === currentChatLanguage;
                    button.classList.toggle('bg-white', active);
                    button.classList.toggle('text-blue-700', active);
                    button.classList.toggle('text-white', !active);
                    button.classList.toggle('opacity-80', !active);
                });
            }

            renderChatSuggestions(chatbotLanguageTexts[currentChatLanguage].suggestions);
        }

        async function sendChatMessage(question) {
            const message = question.trim();
            if (!message || chatbotBusy) return;

            chatbotBusy = true;
            appendChatMessage(message, 'user');
            const loading = appendChatMessage(chatbotLanguageTexts[currentChatLanguage].loading, 'bot');

            try {
                const response = await fetch("{{ route('chatbot.reservas') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ message, language: currentChatLanguage }),
                });

                const data = await response.json();

                if (loading) {
                    loading.remove();
                }

                appendChatMessage(data.reply || 'No pude procesar la pregunta. Intenta de nuevo.', 'bot');
                renderChatSuggestions(data.suggestions || []);
            } catch (error) {
                if (loading) {
                    loading.remove();
                }

                appendChatMessage('No pude conectarme al asistente en este momento. Verifica que el servidor Laravel esté funcionando.', 'bot');
            } finally {
                chatbotBusy = false;
            }
        }

        if (chatbotForm && chatbotInput) {
            chatbotForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const question = chatbotInput.value;
                chatbotInput.value = '';
                sendChatMessage(question);
            });
        }

        if (chatbotSuggestions) {
            chatbotSuggestions.addEventListener('click', (event) => {
                const button = event.target.closest('.chat-suggestion');
                if (!button) return;

                sendChatMessage(button.dataset.question || button.innerText);
            });
        }

        if (chatbotLanguage) {
            chatbotLanguage.addEventListener('click', (event) => {
                const button = event.target.closest('.chat-lang');
                if (!button) return;

                applyChatLanguage(button.dataset.lang);
            });
        }

        applyChatLanguage(currentChatLanguage);

        window.onload = function () {
            const savedTheme = localStorage.getItem('public-theme');

            if (savedTheme === 'light') {
                document.body.classList.remove('dark');
                document.body.classList.add('light');
            } else {
                document.body.classList.add('dark');
                document.body.classList.remove('light');
            }

            applyTheme();
        };

    </script>

</body>
</html>
