<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $config->company_name ?? tenant('business_name') ?? 'Tienda Online' }}</title>
    
    <!-- Usamos los estilos compilados del proyecto -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --primary-color: {{ $config->primary_color ?? '#3b82f6' }};
            --secondary-color: {{ $config->secondary_color ?? '#1e3a8a' }};
            --top-bar-bg: {{ $config->top_bar_bg_color ?? '#000000' }};
            --top-bar-text: {{ $config->top_bar_text_color ?? '#ffffff' }};
        }
        
        /* Custom Overrides using CSS Variables */
        .text-primary-custom { color: var(--primary-color) !important; }
        .bg-primary-custom { background-color: var(--primary-color) !important; }
        .border-primary-custom { border-color: var(--primary-color) !important; }
        
        .text-secondary-custom { color: var(--secondary-color) !important; }
        .bg-secondary-custom { background-color: var(--secondary-color) !important; }
        
        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .btn-primary-custom:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }

        .ecommerce-navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .footer {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer a:hover {
            color: white;
            text-decoration: underline;
        }

        .border-white-10 {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        .top-bar-custom {
            background-color: var(--top-bar-bg);
            color: var(--top-bar-text);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    @if(isset($config) && !$config->is_active && auth()->check() && auth()->user()->hasRole('admin'))
        <div class="bg-warning text-dark text-center py-2 fw-bold small border-bottom border-warning">
            <div class="container">
                <i class="fas fa-tools me-2"></i> MODO MANTENIMIENTO ACTIVO
                <span class="fw-normal ms-2">- La tienda NO es visible para clientes. Usted la ve porque es Administrador.</span>
            </div>
        </div>
    @endif

    <!-- Top Bar -->
    @if($config->top_bar_active && $config->top_bar_text)
        <div class="py-1 text-center fw-bold text-uppercase tracking-widest small top-bar-custom" style="font-size: 0.75rem;">
            <div class="container d-flex justify-content-between align-items-center">
                <span class="d-none d-md-inline opacity-0">Spacer</span> <!-- Spacer -->
                <span>
                    {{ $config->top_bar_text }}
                    @if($config->top_bar_link)
                        <a href="{{ $config->top_bar_link }}" class="text-decoration-underline ms-2" style="color: inherit;">VER MÁS</a>
                    @endif
                </span>
                <div class="d-none d-md-block">
                    <!-- Social Icons small -->
                    @if($config->facebook_url)<a href="{{ $config->facebook_url }}" class="me-3" style="color: inherit;"><i class="fab fa-facebook-f"></i></a>@endif
                    @if($config->instagram_url)<a href="{{ $config->instagram_url }}" class="me-3" style="color: inherit;"><i class="fab fa-instagram"></i></a>@endif
                    @if($config->tiktok_url)<a href="{{ $config->tiktok_url }}" class="me-3" style="color: inherit;"><i class="fab fa-tiktok"></i></a>@endif
                    @if($config->twitter_url)<a href="{{ $config->twitter_url }}" class="me-3" style="color: inherit;"><i class="fab fa-twitter"></i></a>@endif
                </div>
            </div>
        </div>
    @endif

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light ecommerce-navbar sticky-top py-4 border-bottom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-3 me-5" href="{{ route('tenant.shop.index') }}">
                @if($config->logo_path)
                    <img src="{{ asset('storage/' . $config->logo_path) }}" alt="Logo" height="40" class="object-fit-contain">
                @elseif(tenant('logo'))
                     <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" height="40" class="object-fit-contain">
                @else
                    <i class="fas fa-cube text-dark fs-3"></i>
                @endif
                @if(!$config->logo_path && !tenant('logo'))
                    <span class="fw-bold text-dark tracking-wide text-uppercase">{{ $config->company_name ?? tenant('business_name') ?? 'TIENDA' }}</span>
                @endif
            </a>
            
            <button class="navbar-toggler border-0 p-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar in Navbar -->
                <form action="{{ route('tenant.shop.index') }}" method="GET" class="d-flex me-auto my-3 my-lg-0 w-100" style="max-width: 400px;">
                    <div class="input-group border-bottom border-dark">
                        <button class="btn btn-link text-dark p-0 border-0" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        <input class="form-control border-0 bg-transparent shadow-none ps-3 rounded-0" type="search" placeholder="Buscar..." aria-label="Search" name="search" value="{{ request('search') }}">
                    </div>
                </form>

                <ul class="navbar-nav ms-auto align-items-center gap-4">
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-uppercase small tracking-wide text-dark hover-underline {{ request()->routeIs('tenant.shop.index') ? 'text-decoration-underline' : '' }}" href="{{ route('tenant.shop.index') }}">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-uppercase small tracking-wide text-dark hover-underline {{ request()->routeIs('tenant.shop.products') ? 'text-decoration-underline' : '' }}" href="{{ route('tenant.shop.products') }}">Catálogo</a>
                    </li>
                    @if($config->show_categories_section ?? false)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle fw-bold text-uppercase small tracking-wide text-dark" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Categorías
                            </a>
                            <ul class="dropdown-menu border-0 shadow-lg rounded-0 p-0" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item py-3 border-bottom small text-uppercase" href="{{ route('tenant.shop.products') }}">Ver Todas</a></li>
                                @foreach(\App\Models\Tenant\Category::has('products')->take(8)->get() as $cat)
                                    <li><a class="dropdown-item py-2 small text-muted hover-bg-light" href="{{ route('tenant.shop.products', ['category' => $cat->id]) }}">{{ $cat->name }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                    
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link position-relative text-dark p-0" href="{{ route('tenant.shop.cart') }}">
                            <i class="fas fa-shopping-bag fs-4"></i>
                            @php
                                $cartCount = count(session('cart', []));
                            @endphp
                            <span id="navbar-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-dark text-white {{ $cartCount > 0 ? '' : 'd-none' }}" style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                                {{ $cartCount }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Global Cart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update navbar cart count
            window.updateNavbarCartCount = function(count) {
                const badge = document.getElementById('navbar-cart-count');
                if (badge) {
                    badge.innerText = count;
                    if (count > 0) {
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }
            };

            // Function to add item to cart
            window.addToCart = function(arg1, arg2, arg3) {
                let e = null;
                let productId = arg1;
                let quantity = arg2 || 1;

                // Check if first argument is an event object
                if (arg1 && typeof arg1 === 'object' && (arg1 instanceof Event || arg1.preventDefault)) {
                    e = arg1;
                    productId = arg2;
                    quantity = arg3 || 1;
                    
                    e.preventDefault();
                    e.stopPropagation();
                }

                // Show loading state if button was clicked
                let btn = null;
                let originalContent = '';
                
                if (e && e.currentTarget) {
                    btn = e.currentTarget;
                } else if (event && event.currentTarget) {
                    btn = event.currentTarget;
                }

                if(btn && btn.tagName === 'BUTTON') {
                    originalContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                }

                axios.post('{{ route('tenant.shop.cart.add') }}', {
                    id: productId,
                    quantity: quantity,
                    _token: '{{ csrf_token() }}'
                })
                .then(response => {
                    if (response.data.success) {
                        // Update cart count in navbar
                        updateNavbarCartCount(response.data.cartCount);
                        
                        // Show success message
                        if (window.Notify) {
                            window.Notify.success(response.data.message);
                        } else if (window.Swal) {
                            window.Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: response.data.message,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            alert(response.data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    const errorMsg = error.response?.data?.message || 'Error al agregar el producto al carrito';
                    if (window.Notify) {
                        window.Notify.error(errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                })
                .finally(() => {
                    // Reset button state
                    if(btn && btn.tagName === 'BUTTON') {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                });
            };
        });
    </script>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-auto border-top border-dark" id="footer-contact">
        <div class="container py-4">
            <div class="row gy-5">
                <div class="col-lg-5 col-md-12">
                    <h5 class="fw-bold text-uppercase tracking-wide mb-4">{{ $config->company_name ?? tenant('business_name') ?? 'TIENDA' }}</h5>
                    <p class="text-secondary mb-4" style="max-width: 400px;">
                        {{ $config->about_us_text ?? 'Descubre la excelencia en cada detalle. Productos seleccionados para quienes buscan calidad y distinción.' }}
                    </p>
                    <div class="d-flex gap-4">
                        @if($config->facebook_url)
                            <a href="{{ $config->facebook_url }}" target="_blank" class="text-white opacity-50 hover-opacity-100 transition-all"><i class="fab fa-facebook-f fa-lg"></i></a>
                        @endif
                        @if($config->instagram_url)
                            <a href="{{ $config->instagram_url }}" target="_blank" class="text-white opacity-50 hover-opacity-100 transition-all"><i class="fab fa-instagram fa-lg"></i></a>
                        @endif
                        @if($config->whatsapp_number)
                            <a href="https://wa.me/{{ $config->whatsapp_number }}" target="_blank" class="text-white opacity-50 hover-opacity-100 transition-all"><i class="fab fa-whatsapp fa-lg"></i></a>
                        @endif
                    </div>
                </div>
                
                <div class="col-lg-2 col-6">
                    <h6 class="fw-bold text-uppercase tracking-widest small mb-4 text-white-50">Explorar</h6>
                    <ul class="list-unstyled">
                        <li class="mb-3"><a href="{{ route('tenant.shop.index') }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Inicio</a></li>
                        <li class="mb-3"><a href="{{ route('tenant.shop.products') }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Catálogo</a></li>
                        <li class="mb-3"><a href="{{ route('tenant.shop.cart') }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Carrito</a></li>
                    </ul>
                </div>

                @if($config->shipping_policy_link || $config->returns_policy_link || $config->terms_conditions_link)
                <div class="col-lg-2 col-6">
                    <h6 class="fw-bold text-uppercase tracking-widest small mb-4 text-white-50">Ayuda</h6>
                    <ul class="list-unstyled">
                        @if($config->shipping_policy_link)
                            <li class="mb-3"><a href="{{ $config->shipping_policy_link }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Envíos</a></li>
                        @endif
                        @if($config->returns_policy_link)
                            <li class="mb-3"><a href="{{ $config->returns_policy_link }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Devoluciones</a></li>
                        @endif
                        @if($config->terms_conditions_link)
                            <li class="mb-3"><a href="{{ $config->terms_conditions_link }}" class="text-white text-decoration-none opacity-75 hover-opacity-100">Términos</a></li>
                        @endif
                    </ul>
                </div>
                @endif

                <div class="col-lg-3 col-md-12">
                    <h6 class="fw-bold text-uppercase tracking-widest small mb-4 text-white-50">Contacto</h6>
                    <ul class="list-unstyled text-secondary">
                        @php
                            $displayEmail = $config->contact_email ?? tenant('email');
                            $displayPhone = $config->contact_phone ?? tenant('phone');
                            $displayAddress = $config->contact_address ?? tenant('address');
                        @endphp
                        
                        @if($displayEmail)
                            <li class="mb-3 d-flex align-items-start gap-2">
                                <i class="fas fa-envelope mt-1 text-white-50"></i> 
                                <span class="text-white opacity-75">{{ $displayEmail }}</span>
                            </li>
                        @endif
                        @if($displayPhone)
                            <li class="mb-3 d-flex align-items-start gap-2">
                                <i class="fas fa-phone mt-1 text-white-50"></i>
                                <span class="text-white opacity-75">{{ $displayPhone }}</span>
                            </li>
                        @endif
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="fas fa-map-marker-alt mt-1 text-white-50"></i>
                            <span class="text-white opacity-75">{{ $displayAddress ?? 'Ubicación no disponible' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-top border-secondary border-opacity-25 pt-4 mt-5 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="small text-secondary mb-0">
                    &copy; {{ date('Y') }} {{ $config->company_name ?? tenant('business_name') }}. Todos los derechos reservados.
                </p>
                <div class="d-flex gap-3 mt-3 mt-md-0">
                    <i class="fab fa-cc-visa text-secondary fs-4"></i>
                    <i class="fab fa-cc-mastercard text-secondary fs-4"></i>
                    <i class="fab fa-cc-paypal text-secondary fs-4"></i>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
