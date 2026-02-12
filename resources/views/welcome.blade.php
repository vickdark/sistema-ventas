<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Mamba Sales') }} - Premium Edition</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/landing.css'])
</head>
<body class="landing-page text-white">
    <!-- Background Animation -->
    <div class="landing-bg"></div>

    <!-- Navigation -->
    <nav class="landing-nav animate__animated animate__fadeInDown">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('img/mambacode.jpeg') }}" alt="Mamba Code" class="landing-logo">
            <span class="fs-4 fw-bold d-none d-md-block">Mamba<span style="color: var(--mamba-secondary)">Code</span></span>
        </div>
        
        <div class="nav-links d-none d-md-flex">
            <a href="#inicio" class="nav-link-item active">Inicio</a>
            <a href="#caracteristicas" class="nav-link-item">Características</a>
            <a href="#testimonios" class="nav-link-item">Testimonios</a>
            <a href="#contacto" class="nav-link-item">Contacto</a>
        </div>

        <a href="{{ route('central.login') }}" class="mamba-login-trigger" title="Acceso Administrativo">
            <i class="fa-solid fa-fingerprint"></i>
        </a>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section animate__animated animate__fadeInUp" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">
                    <h1 class="hero-title mb-4">
                        Administra tu Negocio <br>
                        <span>Sin Complicaciones</span>
                    </h1>
                    <p class="hero-subtitle mb-0">
                        Una plataforma intuitiva para gestionar tus ventas, controlar tu inventario y cuidar tus finanzas. Todo lo que necesitas en un solo lugar.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="feature-card animate__animated animate__fadeInRight bg-opacity-10" style="background: rgba(255,255,255,0.05);">
                        <div class="feature-icon mb-3">
                            <i class="fa-solid fa-headset text-white"></i>
                        </div>
                        <h3 class="feature-title fs-4">Soporte y Disponibilidad</h3>
                        <p class="feature-desc small mb-0">
                            Disponibilidad del 100% garantizada y servicio de soporte técnico experto 24/7 para tu tranquilidad.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Stats Section -->
    <section class="stats-section animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="container">
            <div class="row text-center">
                <div class="col-6 col-md-3">
                    <span class="stat-number" data-target="360" data-suffix="°">0</span>
                    <span class="stat-label">Control Total</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="stat-number" data-target="99.9" data-suffix="%">0</span>
                    <span class="stat-label">Uptime Garantizado</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="stat-number" data-target="24" data-suffix="/7">0</span>
                    <span class="stat-label">Soporte Técnico</span>
                </div>
                <div class="col-6 col-md-3">
                    <span class="stat-number" data-target="10" data-suffix="x">0</span>
                    <span class="stat-label">Más Rápido</span>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.stat-number');
            const duration = 3000; // Slower animation (3s)

            const animateCounters = () => {
                counters.forEach(counter => {
                    // Cancel any previous animation to prevent overlap
                    if (counter.dataset.animId) {
                        cancelAnimationFrame(counter.dataset.animId);
                    }

                    const target = parseFloat(counter.getAttribute('data-target'));
                    const suffix = counter.getAttribute('data-suffix') || '';
                    const startTime = performance.now();

                    const updateCount = (currentTime) => {
                        const elapsed = currentTime - startTime;
                        const progress = Math.min(elapsed / duration, 1);
                        
                        // Easing function for smooth effect (easeOutCubic)
                        const ease = 1 - Math.pow(1 - progress, 3);
                        
                        const current = target * ease;

                        if (progress < 1) {
                            // Determine decimals based on target (if integer, 0 decimals window, else 1)
                            const decimals = Number.isInteger(target) ? 0 : 1;
                            counter.innerText = current.toFixed(decimals) + suffix;
                            counter.dataset.animId = requestAnimationFrame(updateCount);
                        } else {
                            counter.innerText = target + suffix;
                        }
                    };
                    
                    counter.dataset.animId = requestAnimationFrame(updateCount);
                });
            };

            const resetCounters = () => {
                counters.forEach(counter => {
                    if (counter.dataset.animId) {
                        cancelAnimationFrame(counter.dataset.animId);
                    }
                    const suffix = counter.getAttribute('data-suffix') || '';
                    counter.innerText = '0' + suffix;
                });
            };

            // Trigger animation when stats section is in view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                    } else {
                        resetCounters(); // Reset when out of view to re-animate later
                    }
                });
            }, { threshold: 0.5 });

            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                observer.observe(statsSection);
            }

            // Navbar Scroll Effect
            const nav = document.querySelector('.landing-nav');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }
                
                // Active link handling
                const sections = document.querySelectorAll('section, header, footer');
                const navLinks = document.querySelectorAll('.nav-link-item');
                
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    if (scrollY >= sectionTop - 150) {
                        current = section.getAttribute('id');
                    }
                });

                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href').includes(current)) {
                        link.classList.add('active');
                    }
                });
            });
            
            // Smooth scroll for anchors
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>

    <!-- Features -->
    <section class="features-container" id="caracteristicas">
        <!-- Feature 1: POS -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="feature-icon">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <h3 class="feature-title">Punto de Venta Ágil</h3>
            <p class="feature-desc">
                Registra ventas en segundos. Interfaz diseñada para la velocidad y facilidad de uso en el mostrador.
            </p>
        </div>

        <!-- Feature 2: Inventory -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="feature-icon">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h3 class="feature-title">Control de Inventario</h3>
            <p class="feature-desc">
                Mantén tu stock actualizado automáticamente. Evita pérdidas y quiebres de stock con alertas inteligentes.
            </p>
        </div>

        <!-- Feature 3: Reports -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="feature-icon">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <h3 class="feature-title">Reportes y Finanzas</h3>
            <p class="feature-desc">
                Visualiza ganancias, gastos y flujo de caja en reportes claros para tomar mejores decisiones.
            </p>
        </div>
        
         <!-- Feature 4: Clients -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="feature-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <h3 class="feature-title">Gestión de Clientes</h3>
            <p class="feature-desc">
                Administra cuentas corrientes y créditos. Mantén un historial detallado de tus mejores clientes.
            </p>
        </div>

        <!-- Feature 5: ELT System -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
            <div class="feature-icon">
                <i class="fa-solid fa-cloud-arrow-up"></i>
            </div>
            <h3 class="feature-title">Sistema ELT Integrado</h3>
            <p class="feature-desc">
                Migración masiva inteligente. Importa productos, clientes y proveedores desde Excel con validación automática.
            </p>
        </div>

        <!-- Feature 6: Cash Close -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
            <div class="feature-icon">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <h3 class="feature-title">Cortes de Caja</h3>
            <p class="feature-desc">
                Cuadre diario de efectivo sin estrés. Reportes detallados por turno y cajero.
            </p>
        </div>

        <!-- Feature 7: Suppliers -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.7s;">
            <div class="feature-icon">
                <i class="fa-solid fa-truck-field"></i>
            </div>
            <h3 class="feature-title">Proveedores</h3>
            <p class="feature-desc">
                Gestión completa de abastecimiento. Registra compras y actualiza tu inventario automaticamente.
            </p>
        </div>

        <!-- Feature 8: Offline & Licensing -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.8s;">
            <div class="feature-icon">
                <i class="fa-solid fa-wifi"></i>
            </div>
            <h3 class="feature-title">Modo Offline y Licencias</h3>
            <p class="feature-desc">
                Sigue vendiendo sin internet. Elige entre pago único de licencia de por vida o suscripción mensual flexible.
            </p>
        </div>

        <!-- Feature 9: Security -->
        <div class="feature-card animate__animated animate__fadeInUp" style="animation-delay: 0.9s;">
            <div class="feature-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h3 class="feature-title">Seguridad Total</h3>
            <p class="feature-desc">
                Respaldos automáticos, cierre de cajas programado y protección de datos avanzada.
            </p>
        </div>


    </section>

    <!-- Testimonials -->
    <section class="container py-5 mb-5" id="testimonios">
        <h2 class="text-center hero-title fs-2 mb-5">Lo que dicen nuestros clientes</h2>
        <div class="row g-4">
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="testimonial-card h-100">
                    <p class="testimonial-text">"Desde que usamos Mamba Sales, el control de inventario dejó de ser un dolor de cabeza. ¡El soporte es increíble!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">CM</div>
                        <div>
                            <div class="fw-bold">Carlos Martinez</div>
                            <div class="small text-muted">CEO, TechStore</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="testimonial-card h-100">
                    <p class="testimonial-text">"La función offline nos salvó en varias ocasiones. Es el sistema más robusto que hemos probado."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AL</div>
                        <div>
                            <div class="fw-bold">Ana López</div>
                            <div class="small text-muted">Gerente, Café Aroma</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="testimonial-card h-100">
                    <p class="testimonial-text">"Migrar nuestros datos fue cuestión de minutos con el sistema ELT. Muy recomendado."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">JR</div>
                        <div>
                            <div class="fw-bold">Jorge Ramirez</div>
                            <div class="small text-muted">Director, Importadd</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5 text-center bg-opacity-10" style="background: rgba(255,255,255,0.02);">
        <div class="container py-5">
            <h2 class="hero-title fs-1 mb-4">¿Listo para escalar tu negocio?</h2>
            <p class="fs-5 text-muted mb-5">Únete a cientos de empresas que ya confían en nosotros.</p>
            <a href="mailto:contacto@mambacode.com" class="btn-cyber">
                Contáctanos Ahora
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer" id="contacto">
        <div class="container">
            <div class="row text-center text-md-start">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="d-flex align-items-center gap-2 mb-3 justify-content-center justify-content-md-start">
                        <img src="{{ asset('img/mambacode.jpeg') }}" alt="Mamba Code" style="height: 30px; border-radius: 4px;">
                        <span class="fw-bold">Mamba<span style="color: var(--mamba-secondary)">Code</span></span>
                    </div>
                    <p class="small text-muted">Soluciones tecnológicas de alto nivel para empresas modernas.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold mb-3 text-white">Enlaces Rápidos</h5>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Inicio</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Características</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-muted hover-white">Precios</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="fw-bold mb-3 text-white">Contacto</h5>
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> contacto@mambacode.com</li>
                        <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> +1 234 567 890</li>
                        <li>
                            <a href="#" class="text-muted me-3 fs-5"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" class="text-muted me-3 fs-5"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="text-muted fs-5"><i class="fa-brands fa-linkedin"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-top border-secondary mt-5 pt-4 text-center small text-muted" style="border-color: rgba(255,255,255,0.1) !important;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>