<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acerca del Observatorio | Max Schreier</title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Plus+Jakarta+Sans:wght@200;400;800&family=Syncopate:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { 
                        cinzel: ['Cinzel'], 
                        sans: ['Plus Jakarta Sans'],
                        syncopate: ['Syncopate']
                    },
                    colors: {
                        cosmos: {
                            950: '#02040a',
                            900: '#050a15',
                            primary: '#3b82f6',
                            neon: '#22d3ee',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { transition: background-color 0.8s ease, color 0.5s ease; }
        .glass { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); backdrop-filter: blur(15px); }
        .light .glass { background: rgba(255, 255, 255, 0.7); border: 1px solid rgba(0, 0, 0, 0.05); }
        
        .text-gradient { 
            background: linear-gradient(135deg, #fff 0%, #3b82f6 50%, #22d3ee 100%); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; 
        }

        .reveal { opacity: 0; transform: translateY(30px); transition: 1.2s cubic-bezier(0.2, 0.8, 0.2, 1); }
        .reveal.active { opacity: 1; transform: translateY(0); }

        /* Contenedor de imagen responsivo con placeholder */
        .image-slot {
            width: 100%;
            aspect-ratio: 16/9;
            background: rgba(255,255,255,0.05);
            border: 2px dashed rgba(255,255,255,0.1);
            border-radius: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        .image-slot img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body class="bg-cosmos-950 text-white font-sans dark overflow-x-hidden">

<div id="particles-js" class="fixed inset-0 z-0 pointer-events-none"></div>

<!-- BARRA DE NAVEGACIÓN CON BOTÓN "VOLVER" ARRIBA -->
<header class="fixed top-0 w-full z-[100] p-4">
    <nav class="max-w-7xl mx-auto flex gap-4 items-center">
        
        <!-- BOTÓN VOLVER (NUEVA UBICACIÓN) -->
        <a href="{{ route('bienvenido') }}" class="glass h-12 px-6 rounded-full flex items-center gap-3 text-cosmos-neon hover:bg-cosmos-neon hover:text-cosmos-950 transition-all group">
            <i class="fas fa-arrow-left text-xs"></i>
            <span class="text-[10px] font-black uppercase tracking-widest hidden md:block">Inicio</span>
        </a>

        <div class="glass flex-grow rounded-full px-6 py-3 flex justify-between items-center">
            <div class="flex flex-col">
                <span class="font-syncopate font-black tracking-tighter text-xs md:text-sm uppercase">MAX <span class="text-cosmos-neon">SCHREIER</span></span>
                <span class="text-[6px] md:text-[7px] uppercase tracking-[0.5em] text-cosmos-primary font-bold">UMSA Astrophysics</span>
            </div>

            <div class="flex items-center gap-6">
                <!-- Selector de Tema -->
                <button onclick="toggleTheme()" class="relative w-12 h-6 bg-white/10 rounded-full flex items-center px-1 border border-white/10">
                    <div id="toggle-circle" class="w-4 h-4 bg-cosmos-neon rounded-full transition-all duration-500 flex items-center justify-center">
                        <i id="theme-icon" class="fas fa-moon text-[8px] text-cosmos-950"></i>
                    </div>
                </button>
            </div>
        </div>
    </nav>
</header>

<!-- HERO -->
<section class="relative h-[50vh] flex items-center justify-center text-center px-6">
    <div class="relative z-10">
        <h1 class="text-5xl md:text-8xl font-cinzel font-black tracking-tighter reveal active">
            EL <span class="text-gradient italic">OBSERVATORIO</span>
        </h1>
        <p class="text-[10px] md:text-xs uppercase tracking-[0.6em] mt-4 opacity-50 reveal active">Exploración e Investigación Científica • UMSA</p>
    </div>
</section>

<!-- SECCIÓN DE HISTORIA E INVESTIGACIÓN -->
<section id="historia" class="py-10 px-6 max-w-7xl mx-auto space-y-24">
    
    <!-- Bloque 1: Antecedentes -->
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="reveal">
            <h2 class="text-3xl md:text-4xl font-cinzel font-black mb-6 text-cosmos-neon">Antecedentes <br><span class="text-white italic">Institucionales</span></h2>
            <div class="glass p-8 rounded-[2.5rem] border-l-2 border-cosmos-primary">
                <p class="text-sm md:text-base text-white/70 leading-relaxed">
                    {{ $info->historia ?? 'Aquí se detallará la trayectoria del observatorio desde su fundación bajo la Universidad Mayor de San Andrés.' }}
                </p>
            </div>
        </div>

        <!-- ESPACIO PARA FOTO 1 -->
        <div class="reveal">
            <div class="image-slot group">
                <!-- 
                     INSTRUCCIONES PARA FOTO:
                     1. Guarda tu imagen en: public/assets/img/observatorio/
                     2. Formato recomendado: .jpg o .webp (pesan menos)
                     3. Tamaño recomendado: 1200x800px
                     4. Reemplaza el <img> de abajo cuando tengas la foto.
                -->
                <img src="https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=1200" alt="Fachada Observatorio">
                <div class="absolute inset-0 bg-cosmos-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="text-[10px] font-bold tracking-[0.3em] uppercase">Vista Institucional</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bloque 2: Investigación (Invertido) -->
    <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div class="order-2 lg:order-1 reveal">
            <!-- ESPACIO PARA FOTO 2 -->
            <div class="image-slot group">
                <!-- Sugerencia: Foto del Telescopio o equipos de avanzada -->
                <img src="https://images.unsplash.com/photo-1543722530-d2c3201371e7?q=80&w=1200" alt="Equipamiento">
                <div class="absolute inset-0 bg-cosmos-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="text-[10px] font-bold tracking-[0.3em] uppercase">Tecnología Avanzada</span>
                </div>
            </div>
        </div>

        <div class="order-1 lg:order-2 reveal">
            <h2 class="text-3xl md:text-4xl font-cinzel font-black mb-6 text-cosmos-neon">Líneas de <br><span class="text-white italic">Investigación</span></h2>
            <div class="glass p-8 rounded-[2.5rem] border-r-2 border-cosmos-primary text-right">
                <p class="text-sm md:text-base text-white/70 leading-relaxed">
                    Investigación científica aplicada en astrofísica, monitoreo solar y estudios atmosféricos realizados por docentes y estudiantes de la UMSA.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- UBICACIÓN (MAPA) -->
<section id="ubicacion" class="py-20 px-6 max-w-7xl mx-auto">
    <div class="text-center mb-10 reveal">
        <h2 class="text-2xl font-cinzel font-black tracking-widest uppercase">Ubicación</h2>
        <p class="text-[10px] opacity-40 mt-2">Cota Cota • Calle 27 • La Paz, Bolivia</p>
    </div>
    <div class="h-[400px] rounded-[3rem] overflow-hidden glass p-2 reveal">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3824.978502391696!2d-68.0673322239634!3d-16.527181084224795!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f212217c1815d%3A0x6d9f82607f230f3f!2sObservatorio%20Astron%C3%B3mico%20Max%20Schreier!5e0!3m2!1ses!2sbo!4v1714675000000!5m2!1ses!2sbo" class="w-full h-full rounded-[2.5rem] grayscale invert-[0.9] hue-rotate-[180deg] border-0" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<footer class="py-12 text-center">
    <p class="text-[8px] font-black uppercase tracking-[0.5em] text-white/20">© 2026 Observatorio Max Schreier • Facultad de Ciencias Puras y Naturales</p>
</footer>

<script>
    function initParticles(color) {
        if(window.pJSDom && window.pJSDom.length > 0) {
            window.pJSDom[0].pJS.fn.vendors.destroypJS();
            window.pJSDom = [];
        }
        particlesJS('particles-js', {
            particles: {
                number: { value: 40 },
                color: { value: color },
                shape: { type: "circle" },
                opacity: { value: 0.2 },
                size: { value: 1.5 },
                line_linked: { enable: true, distance: 150, color: color, opacity: 0.1, width: 1 },
                move: { enable: true, speed: 0.5 }
            },
            retina_detect: true
        });
    }

    function toggleTheme() {
        const body = document.body;
        const circle = document.getElementById('toggle-circle');
        const icon = document.getElementById('theme-icon');
        body.classList.toggle('dark');
        body.classList.toggle('light');
        
        if (body.classList.contains('dark')) {
            body.classList.add('bg-cosmos-950', 'text-white');
            body.classList.remove('bg-slate-50', 'text-slate-900');
            circle.style.transform = 'translateX(0)';
            icon.classList.replace('fa-sun', 'fa-moon');
            initParticles('#3b82f6');
        } else {
            body.classList.remove('bg-cosmos-950', 'text-white');
            body.classList.add('bg-slate-50', 'text-slate-900');
            circle.style.transform = 'translateX(24px)';
            icon.classList.replace('fa-moon', 'fa-sun');
            initParticles('#020617');
        }
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) entry.target.classList.add('active');
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    document.addEventListener('DOMContentLoaded', () => initParticles('#3b82f6'));
</script>

</body>
</html>