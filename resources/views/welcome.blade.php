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
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pages/landing.css', 'resources/js/pages/landing.js'])
</head>
<body class="landing-page text-white">
    <!-- Background Animation -->
    <div class="landing-bg"></div>

    <!-- Navigation -->
    <nav class="landing-nav animate__animated animate__fadeInDown">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('img/mambacode.jpeg') }}" alt="Mamba Code" class="landing-logo">
                <span class="fs-4 fw-bold d-none d-md-block">Mamba<span style="color: var(--mamba-secondary)">Code</span></span>
            </div>
            
            <div class="nav-links d-none d-md-flex">
                <a href="#inicio" class="nav-link-item active">Inicio</a>
                <a href="#caracteristicas" class="nav-link-item">Características</a>
                <a href="#precios" class="nav-link-item">Precios</a>
                <a href="#testimonios" class="nav-link-item">Testimonios</a>
                <a href="#contacto" class="nav-link-item">Contacto</a>
            </div>

            <a href="{{ route('central.login') }}" class="mamba-login-trigger" title="Acceso Administrativo">
                <i class="fa-solid fa-fingerprint"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section animate__animated animate__fadeInUp" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 text-center text-lg-start mb-5 mb-lg-0">
                    <h1 class="hero-title mb-4">
                        Soluciones Tecnológicas <br>
                        <span>Evolucionamos tu Software</span>
                    </h1>
                    <p class="hero-subtitle mb-0">
                        Analizamos la lógica de tu negocio para transformarla en soluciones digitales a medida. Diseñamos plataformas inteligentes para automatizar tus procesos y escalar tu infraestructura, incluso si empiezas desde cero.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="d-flex flex-column gap-4">
                        <div class="feature-card hero-feature-card animate__animated animate__fadeInRight bg-opacity-10">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-microchip text-white"></i>
                            </div>
                            <h3 class="feature-title fs-4">Análisis y Consultoría</h3>
                            <p class="feature-desc small mb-0">
                                Estudiamos profundamente tu lógica de negocio para diseñar la solución tecnológica ideal, construyendo desde cero la infraestructura que necesitas.
                            </p>
                        </div>
                        
                        <div class="feature-card hero-feature-card animate__animated animate__fadeInRight bg-opacity-10" style="animation-delay: 0.2s;">
                            <div class="feature-icon mb-3">
                                <i class="fa-solid fa-headset text-white"></i>
                            </div>
                            <h3 class="feature-title fs-4">Soporte y Disponibilidad</h3>
                            <p class="feature-desc small mb-0">
                                Disponibilidad garantizada y servicio de soporte técnico experto 24/7 para asegurar la continuidad de tu ecosistema tecnológico.
                            </p>
                        </div>
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

    <!-- Pricing Section -->
    <section class="pricing-section" id="precios">
        <h2 class="text-center hero-title fs-1 mb-2">Planes Flexibles</h2>
        <p class="text-center text-muted mb-5">Elige el modelo que mejor se adapte al despliegue de tu infraestructura.</p>
        
        <div class="pricing-grid">
            <!-- Subscription Plan -->
            <div class="pricing-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <h3 class="fs-4 fw-bold">Suscripción Flexible</h3>
                <div class="mb-3">
                    <span class="pricing-price fs-2">$29<small class="fs-6 text-muted">/30 días</small></span>
                </div>
                <p class="small text-muted mb-4">Planes adaptables de 30, 90 o 365 días según tus requerimientos de escalabilidad.</p>
                <ul class="pricing-features">
                    <li><i class="fa-solid fa-shield-halved"></i> Seguridad de la Información</li>
                    <li><i class="fa-solid fa-database"></i> Integridad de Datos y Backups</li>
                    <li><i class="fa-solid fa-bolt"></i> Alto Rendimiento Garantizado</li>
                    <li><i class="fa-solid fa-headset"></i> Soporte Técnico Especializado</li>
                </ul>
                <a href="#contacto" class="btn-cyber w-100">Iniciar Despliegue</a>
            </div>

            <!-- Lifetime License -->
            <div class="pricing-card featured animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="badge-premium">Recomendado</div>
                <h3 class="fs-4 fw-bold">Licencia de Software</h3>
                <div class="mb-3">
                    <span class="pricing-price fs-2">$499<small class="fs-6 text-muted">/Pago único</small></span>
                </div>
                <p class="small text-white-50 mb-4">Adquiere el uso perpetuo del software con servicios de mantenimiento y evolutivos anuales.</p>
                <ul class="pricing-features">
                    <li><i class="fa-solid fa-check-double"></i> Pago único por Uso de Software</li>
                    <li><i class="fa-solid fa-gears"></i> Mantenimiento de Software*</li>
                    <li class="small text-muted" style="font-size: 0.75rem; margin-top: -0.5rem; padding-left: 1.7rem; margin-bottom: 0.5rem;">*Requiere un pequeño pago anual</li>
                    <li><i class="fa-solid fa-headset"></i> Soporte Técnico Especializado</li>
                    <li><i class="fa-solid fa-chalkboard-user"></i> Capacitaciones Personalizadas</li>
                    <li><i class="fa-solid fa-code-branch"></i> Mejoras e Integraciones**</li>
                    <li class="small text-muted" style="font-size: 0.75rem; margin-top: -0.5rem; padding-left: 1.7rem; margin-bottom: 0.5rem;">**Sujeto a presupuesto por desarrollo</li>
                    <li><i class="fa-solid fa-shield-halved"></i> Máxima Seguridad e Integridad</li>
                    <li><i class="fa-solid fa-server"></i> Infraestructura de Backups Incluida</li>
                </ul>
                <a href="#contacto" class="btn-cyber w-100">Adquirir Licencia Vitalicia</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="container py-5 mb-5" id="testimonios">
        <h2 class="text-center hero-title fs-2 mb-5">Lo que dicen nuestros clientes</h2>
        <div class="row g-4">
            <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="testimonial-card h-100">
                    <p class="testimonial-text">"La escalabilidad de este software es incomparable. Implementamos la solución en tiempo récord."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">CM</div>
                        <div>
                            <div class="fw-bold">Carlos Martinez</div>
                            <div class="small text-muted">CTO, TechFlow</div>
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
            <h2 class="hero-title fs-1 mb-4">¿Listo para evolucionar tu infraestructura?</h2>
            <p class="fs-5 text-muted mb-5">Implementa soluciones de software de alto impacto y escala tu ecosistema tecnológico hoy mismo.</p>
            <a href="mailto:contacto@mambacode.com" class="btn-cyber">
                Solicitar Consultoría Técnica
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