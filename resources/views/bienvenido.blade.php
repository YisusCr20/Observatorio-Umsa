<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISOBS | Observatorio Max Schreier</title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;900&family=Plus+Jakarta+Sans:wght@200;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { cinzel: ['Cinzel'], sans: ['Plus Jakarta Sans'] },
                    colors: {
                        cosmos: {
                            950: '#02040a',
                            primary: '#3b82f6',
                            neon: '#22d3ee',
                            violet: '#7c3aed'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Glassmorphism avanzado */
        .glass { background: rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.1); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }
        .light .glass { background: rgba(255,255,255,0.7); border:1px solid rgba(0,0,0,0.1); }
        
        body { transition: background 0.8s, color 0.5s; }
        .text-gradient { background: linear-gradient(135deg,#fff,#3b82f6,#22d3ee); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .light .text-gradient { background: linear-gradient(135deg,#0f172a,#3b82f6,#7c3aed); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        
        /* Animaciones */
        .float-anim { animation: floating 4s ease-in-out infinite; }
        @keyframes floating {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-20px) scale(1.02); }
        }

        .slide { display: none; opacity: 0; transition: opacity 1s ease-in-out, transform 1s ease; transform: scale(0.95); }
        .slide.active { display: flex; opacity: 1; transform: scale(1); }
        
        .bot-glow { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); animation: pulse-blue 2s infinite; }
        @keyframes pulse-blue {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }

        /* Redes Sociales */
        .social-btn { 
            @apply w-10 h-10 md:w-12 md:h-12 flex items-center justify-center rounded-full glass transition-all duration-300 text-white hover:bg-cosmos-primary hover:scale-110 shadow-lg;
        }

        /* Navbar Scroll Effect */
        .nav-scrolled { padding-top: 0.5rem; padding-bottom: 0.5rem; background: rgba(2, 4, 10, 0.8); border-color: transparent; }
        .light .nav-scrolled { background: rgba(255, 255, 255, 0.9); }
    </style>
</head>

<body class="bg-cosmos-950 text-white font-sans dark overflow-x-hidden">

<div id="particles-js" class="fixed inset-0 z-10 pointer-events-none"></div>

<!-- FONDO PARALLAX -->
<div class="fixed inset-0 z-0">
    <div id="slide-dark" class="absolute inset-0 bg-cover bg-center opacity-100 transition duration-1000 scale-105" style="background-image:url('https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?q=80&w=2000');"></div>
    <div id="slide-light" class="absolute inset-0 bg-cover bg-center opacity-0 transition duration-1000 scale-105" style="background-image:url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?q=80&w=2000');"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-cosmos-950/50 to-cosmos-950 dark:to-cosmos-950/95"></div>
</div>

<!-- REDES SOCIALES (ESQUINA INFERIOR IZQUIERDA) -->
<div class="fixed left-4 bottom-4 md:left-8 md:bottom-8 z-40 flex md:flex-col gap-3 items-center">
    <a href="#" class="social-btn" title="Facebook"><i class="fab fa-facebook-f"></i></a>
    <a href="#" class="social-btn" title="Instagram"><i class="fab fa-instagram"></i></a>
    <a href="#" class="social-btn" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
</div>

<!-- NAVEGACIÓN PRINCIPAL -->
<header class="fixed top-0 w-full z-50 p-4 md:p-6 transition-all duration-500" id="navbar">
    <nav class="max-w-7xl mx-auto glass rounded-2xl px-5 md:px-8 py-3 md:py-4 flex justify-between items-center shadow-2xl transition-all duration-500" id="nav-container">
        
        <!-- Logo -->
        <div class="flex flex-col relative z-50 cursor-pointer hover:scale-105 transition-transform" onclick="window.scrollTo(0,0)">
            <span class="font-cinzel font-black tracking-widest text-base md:text-xl text-white light:text-cosmos-950 drop-shadow-md">MAX SCHREIER</span>
            <span class="text-[7px] md:text-[9px] uppercase tracking-[0.4em] text-cosmos-neon font-bold">Observatorio • UMSA</span>
        </div>

        <!-- Botones Móvil (Menú + Tema) -->
        <div class="flex items-center gap-4 lg:hidden relative z-50">
            <button onclick="toggleTheme()" class="w-10 h-5 bg-white/10 rounded-full flex items-center px-1 transition-all border border-white/20">
                <div id="toggle-circle-mobile" class="w-3 h-3 bg-white rounded-full transition-transform duration-500"></div>
            </button>
            <button id="mobile-menu-btn" class="text-white text-2xl focus:outline-none hover:text-cosmos-neon transition-colors">
                <i class="fas fa-bars-staggered"></i>
            </button>
        </div>

        <!-- Enlaces Desktop -->
        <div class="hidden lg:flex items-center gap-8 text-[11px] font-bold uppercase tracking-[0.2em]">
            <a href="{{ route('acerca') }}" class="relative group py-2">
                <span class="text-white/80 group-hover:text-white transition-colors">Acerca de</span>
                <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
            </a>
            <a href="{{ route('investigacion') }}" class="relative group py-2">
                <span class="text-white/80 group-hover:text-white transition-colors">Investigación</span>
                <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
            </a>
            <a href="{{ route('eventos') }}" class="relative group py-2">
                <span class="text-white/80 group-hover:text-white transition-colors">Eventos</span>
                <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
            </a>
            <a href="#" class="relative group py-2">
                <span class="text-white/80 group-hover:text-white transition-colors">Contactos</span>
                <span class="absolute bottom-0 left-1/2 w-0 h-[2px] bg-cosmos-neon group-hover:w-full group-hover:left-0 transition-all duration-300"></span>
            </a>
            
            <div class="w-[1px] h-6 bg-white/20 mx-2"></div> <!-- Separador -->

            <button onclick="toggleTheme()" class="w-12 h-6 bg-white/10 rounded-full flex items-center px-1 transition-all border border-white/20 hover:bg-white/20">
                <div id="toggle-circle" class="w-4 h-4 bg-white rounded-full transition-transform duration-500 shadow-md"></div>
            </button>

            <div class="flex items-center gap-3 ml-2">
                <a href="{{ route('login') }}" class="bg-transparent border border-white/20 px-6 py-2.5 rounded-full hover:bg-white/10 hover:border-white/50 transition-all text-white backdrop-blur-sm">Ingresar</a>
                <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-600 to-cosmos-primary px-7 py-2.5 rounded-full text-white hover:scale-105 hover:shadow-[0_0_20px_rgba(59,130,246,0.6)] transition-all duration-300">Registro</a>
            </div>
        </div>
    </nav>
</header>

<!-- MENÚ MÓVIL DESPLEGABLE -->
<div id="mobile-menu" class="fixed inset-0 bg-cosmos-950/98 backdrop-blur-2xl z-40 transform translate-x-full transition-transform duration-500 lg:hidden flex flex-col justify-center items-center">
    <div class="flex flex-col items-center gap-8 text-lg font-cinzel font-bold uppercase tracking-widest w-full px-8">
        <a href="{{ route('acerca') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Acerca de</a>
        <a href="{{ route('investigacion') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Investigación</a>
        <a href="{{ route('eventos') }}" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 border-b border-white/10">Eventos</a>
        <a href="#" class="text-white/70 hover:text-cosmos-neon hover:scale-110 transition-all w-full text-center py-4 mb-4">Contactos</a>
        
        <div class="flex flex-col gap-4 w-full max-w-xs mt-4">
            <a href="{{ route('login') }}" class="glass w-full py-4 rounded-xl text-center text-white text-sm tracking-[0.2em] hover:bg-white/10 transition-colors">INICIAR SESIÓN</a>
            <a href="{{ route('register') }}" class="bg-cosmos-primary w-full py-4 rounded-xl text-center text-white text-sm tracking-[0.2em] shadow-lg shadow-blue-500/30">CREAR CUENTA</a>
        </div>
    </div>
</div>

<!-- MAIN HERO SLIDER -->
<main class="relative z-20 h-screen max-w-7xl mx-auto flex items-center justify-center px-6 pt-16 md:pt-20">
    
    <!-- Slide 1: Observa el Cielo -->
    <div class="slide active flex-col-reverse md:flex-row items-center justify-center gap-8 md:gap-12 w-full h-full pb-20 md:pb-0">
        <div class="flex-1 text-center md:text-left flex flex-col items-center md:items-start">
            <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-cinzel font-black leading-tight drop-shadow-2xl">
                <span class="text-gradient italic">OBSERVA</span><br/> EL CIELO
            </h1>
            <p class="mt-4 md:mt-6 text-white/70 text-sm md:text-lg max-w-lg leading-relaxed font-light">
                Descubre los secretos del cosmos con tecnología de vanguardia en el corazón de La Paz.
            </p>
        </div>
        <div class="flex-1 flex justify-center items-center w-full">
            <!-- Imagen responsiva con soporte JPEG -->
            <img src="{{ asset('img/slide1.jpg') }}" 
                 class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] object-cover rounded-3xl float-anim drop-shadow-[0_0_30px_rgba(59,130,246,0.3)] border border-white/10" 
                 alt="Telescopio Principal">
        </div>
    </div>

    <!-- Slide 2: Ciencia UMSA -->
    <div class="slide flex-col-reverse md:flex-row items-center justify-center gap-8 md:gap-12 w-full h-full pb-20 md:pb-0">
        <div class="flex-1 text-center md:text-left flex flex-col items-center md:items-start">
            <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-cinzel font-black leading-tight drop-shadow-2xl">
                <span class="text-gradient italic">CIENCIA</span><br/> UMSA
            </h1>
            <p class="mt-4 md:mt-6 text-white/70 text-sm md:text-lg max-w-lg leading-relaxed font-light">
                Facilitamos la investigación astronómica para la comunidad universitaria.
            </p>
        </div>
        <div class="flex-1 flex justify-center items-center w-full">
            <img src="{{ asset('img/slide2.jpg') }}" 
                 class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] object-cover rounded-full float-anim drop-shadow-[0_0_30px_rgba(124,58,237,0.3)] border-2 border-purple-500/30" 
                 alt="Investigación">
        </div>
    </div>

    <!-- Slide 3: Espacio Infinito -->
    <div class="slide flex-col-reverse md:flex-row items-center justify-center gap-8 md:gap-12 w-full h-full pb-20 md:pb-0">
        <div class="flex-1 text-center md:text-left flex flex-col items-center md:items-start">
            <h1 class="text-4xl sm:text-5xl md:text-7xl lg:text-8xl font-cinzel font-black leading-tight drop-shadow-2xl">
                <span class="text-gradient italic">ESPACIO</span><br/> INFINITO
            </h1>
            <p class="mt-4 md:mt-6 text-white/70 text-sm md:text-lg max-w-lg leading-relaxed font-light">
                Explora las galaxias lejanas desde nuestra ubicación privilegiada.
            </p>
        </div>
        <div class="flex-1 flex justify-center items-center w-full">
            <img src="{{ asset('img/slide3.jpg') }}" 
                 class="w-64 h-64 sm:w-80 sm:h-80 md:w-[450px] md:h-[450px] object-cover rounded-[2rem] rotate-3 float-anim drop-shadow-[0_0_30px_rgba(34,211,238,0.3)]" 
                    alt="Nebulosa">
            </div>
    </div>
</main>

    <!-- Indicadores -->
    <div class="absolute bottom-8 md:bottom-12 left-1/2 -translate-x-1/2 flex gap-4" id="dot-container"></div>
</main>

<!-- BOTON CHAT -->
<div class="fixed bottom-4 right-4 md:bottom-8 md:right-8 z-50">
    <button onclick="toggleChat()" class="bot-glow w-14 h-14 md:w-16 md:h-16 bg-cosmos-primary rounded-full flex items-center justify-center transition-transform hover:scale-110 active:scale-95 group shadow-2xl">
        <svg class="w-7 h-7 md:w-8 md:h-8 text-white transition-all group-hover:rotate-12 group-hover:scale-110" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C7.58 2 4 5.58 4 10V12C2.9 12 2 12.9 2 14V16C2 17.1 2.9 18 4 18V19C4 20.66 5.34 22 7 22H17C18.66 22 20 20.66 20 19V18C21.1 18 22 17.1 22 16V14C22 12.9 21.1 12 20 12V10C20 5.58 16.42 2 12 2Z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="1.5"/>
            <rect x="7" y="10" width="10" height="6" rx="2" stroke="currentColor" stroke-width="1.5" fill="currentColor" fill-opacity="0.3"/>
            <circle cx="9.5" cy="13" r="1" fill="currentColor"/><circle cx="14.5" cy="13" r="1" fill="currentColor"/>
            <path d="M11 7H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
    </button>
</div>

<script>
    // --- LÓGICA DEL SLIDER ---
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dotContainer = document.getElementById('dot-container');

    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = `dot w-10 h-2 md:w-12 md:h-2 rounded-full transition-all duration-500 ${i === 0 ? 'bg-cosmos-primary shadow-[0_0_10px_#3b82f6]' : 'bg-white/20 hover:bg-white/40'}`;
        dot.onclick = () => setSlide(i);
        dotContainer.appendChild(dot);
    });

    const dots = document.querySelectorAll('.dot');

    function setSlide(index) {
        slides[currentSlide].classList.remove('active');
        dots[currentSlide].classList.replace('bg-cosmos-primary', 'bg-white/20');
        dots[currentSlide].classList.remove('shadow-[0_0_10px_#3b82f6]');
        
        currentSlide = index;
        
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.replace('bg-white/20', 'bg-cosmos-primary');
        dots[currentSlide].classList.add('shadow-[0_0_10px_#3b82f6]');
    }

    setInterval(() => {
        setSlide((currentSlide + 1) % slides.length);
    }, 6000); 

    // --- TEMA CLARO/OSCURO ---
    function applyTheme(){
        let isDark = document.body.classList.contains('dark');
        let darkBg = document.getElementById('slide-dark');
        let lightBg = document.getElementById('slide-light');
        let circles = [document.getElementById('toggle-circle'), document.getElementById('toggle-circle-mobile')];

        if(isDark){
            if(darkBg && lightBg) { darkBg.style.opacity = 1; lightBg.style.opacity = 0; }
            circles.forEach(c => { if(c) c.style.transform = 'translateX(0)'; });
            initParticles('#3b82f6');
        } else {
            if(darkBg && lightBg) { darkBg.style.opacity = 0; lightBg.style.opacity = 1; }
            circles.forEach(c => { if(c) c.style.transform = 'translateX(100%)'; });
            initParticles('#1e293b');
        }
    }

    function toggleTheme(){
        document.body.classList.toggle('dark');
        document.body.classList.toggle('light');
        applyTheme();
    }

    // --- PARTÍCULAS ---
    function initParticles(color) {
        if(window.pJSDom && window.pJSDom.length > 0) { 
            window.pJSDom[0].pJS.fn.vendors.destroypJS(); 
            window.pJSDom = []; 
        }
        
        const isMobile = window.innerWidth < 768;
        
        particlesJS('particles-js', {
            particles: {
                number: { value: isMobile ? 25 : 60 },
                color: { value: color },
                shape: { type: "circle" },
                opacity: { value: 0.4 },
                size: { value: isMobile ? 1 : 2 },
                line_linked: { enable: true, distance: 150, color: color, opacity: 0.15, width: 1 },
                move: { speed: 0.6 }
            }
        });
    }

    // --- MENÚ MÓVIL ---
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    let menuOpen = false;

    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', () => {
            menuOpen = !menuOpen;
            if(menuOpen) {
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

    // --- EFECTO NAVBAR AL HACER SCROLL ---
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

    function toggleChat(){ alert("Soporte en línea conectado."); }

    // Inicializar
    window.onload = applyTheme;
    window.addEventListener('resize', () => initParticles(document.body.classList.contains('dark') ? '#3b82f6' : '#1e293b'));
</script>

</body>
</html>