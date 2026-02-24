@extends('tenant.ecommerce.layout')

@section('content')

<!-- Hero Section -->
@if($config->banner_path)
    <div class="position-relative w-100 overflow-hidden" style="height: 75vh; min-height: 500px;">
        <img src="{{ asset('storage/' . $config->banner_path) }}" alt="Banner" class="w-100 h-100 object-fit-cover filter-brightness-75 animate-zoom-in">
        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column align-items-center justify-content-center text-center p-4 bg-dark-gradient">
            <span class="text-uppercase text-white fw-bold tracking-widest mb-3 animate-fade-in-up small">Nueva Colección 2024</span>
            <h1 class="text-white fw-bold display-1 mb-4 text-shadow animate-fade-in-up delay-100">{{ $config->hero_title ?? tenant('business_name') ?? $config->company_name ?? 'Bienvenido' }}</h1>
            @if($config->hero_subtitle)
                <p class="text-white fs-4 mb-5 text-shadow animate-fade-in-up delay-200 w-md-50 mx-auto opacity-90">{{ $config->hero_subtitle }}</p>
            @endif
            <div class="d-flex gap-3 animate-fade-in-up delay-300">
                <a href="#products-section" class="btn btn-light btn-lg rounded-0 px-5 py-3 fw-bold shadow-lg hover-scale text-uppercase tracking-wide">
                    Ver Productos
                </a>
                <a href="{{ route('tenant.shop.products') }}" class="btn btn-outline-light btn-lg rounded-0 px-5 py-3 fw-bold hover-bg-white hover-text-dark transition-all text-uppercase tracking-wide">
                    Catálogo
                </a>
            </div>
        </div>
    </div>
@endif

<!-- Categories Section (Collections) -->
@if($config->show_categories_section && isset($categories) && $categories->count() > 0)
    <div class="container py-5 my-5" id="categories-section">
        <div class="text-center mb-5">
            <span class="text-primary-custom text-uppercase fw-bold tracking-widest small">Categorías</span>
            <h2 class="fw-bold display-5 mt-2">Nuestras Colecciones</h2>
            <div class="divider mx-auto mt-3 bg-primary-custom"></div>
        </div>
        
        <div class="row g-4">
            @foreach($categories->take(3) as $category)
                <div class="col-md-4">
                    <a href="{{ route('tenant.shop.products', ['category' => $category->id]) }}" class="text-decoration-none group">
                        <div class="card border-0 text-white overflow-hidden rounded-0 h-100 shadow-sm position-relative" style="min-height: 350px;">
                             <!-- Placeholder or Category Image if available -->
                            <div class="w-100 h-100 bg-secondary d-flex align-items-center justify-content-center position-absolute top-0 start-0 group-hover-scale transition-transform duration-700">
                                <i class="fas fa-tags fa-3x opacity-25"></i>
                            </div>
                            <div class="card-img-overlay d-flex flex-column justify-content-end p-4 bg-gradient-to-t">
                                <h3 class="fw-bold mb-1 text-white">{{ $category->name }}</h3>
                                <p class="text-white-50 mb-0 group-hover-translate-x transition-transform">{{ $category->products_count ?? 0 }} Productos <i class="fas fa-arrow-right ms-2 opacity-0 group-hover-opacity-100 transition-opacity"></i></p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Featured Split Section -->
@if($config->show_featured_section)
<div class="container-fluid bg-light py-5 my-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="pe-lg-5">
                    <span class="badge bg-primary-custom rounded-0 px-3 py-2 mb-3 text-uppercase tracking-wide">Destacado</span>
                    <h2 class="display-4 fw-bold mb-4">{{ $config->featured_title ?? 'Calidad Premium' }}</h2>
                    <p class="lead text-secondary mb-4">{{ $config->featured_description ?? 'Descubre nuestra selección exclusiva de productos diseñados para destacar. Materiales de primera calidad y un diseño inigualable.' }}</p>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-primary-custom me-3 fa-lg"></i> Productos seleccionados cuidadosamente</li>
                        <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-primary-custom me-3 fa-lg"></i> Atención personalizada</li>
                        <li class="mb-3 d-flex align-items-center"><i class="fas fa-check-circle text-primary-custom me-3 fa-lg"></i> Garantía de satisfacción</li>
                    </ul>
                    <a href="{{ $config->featured_btn_link ?? route('tenant.shop.products') }}" class="btn btn-dark btn-lg rounded-0 px-5 py-3 text-uppercase tracking-wide hover-scale">
                        {{ $config->featured_btn_text ?? 'Comprar Ahora' }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2">
                <div class="position-relative">
                    <div class="ratio ratio-4x3 bg-white shadow-lg rounded-0 overflow-hidden">
                         @if(isset($products) && $products->first() && $products->first()->image)
                            <img src="{{ asset('storage/' . $products->first()->image) }}" class="object-fit-cover w-100 h-100" alt="Featured">
                        @else
                            <div class="d-flex align-items-center justify-content-center bg-secondary text-white h-100">
                                <i class="fas fa-star fa-5x opacity-50"></i>
                            </div>
                        @endif
                    </div>
                    <!-- Floating Badge -->
                    <div class="position-absolute bottom-0 start-0 bg-white p-4 shadow-lg m-4 d-none d-md-block border-start border-5 border-primary-custom">
                        <p class="mb-0 text-uppercase text-muted small fw-bold">Desde</p>
                        <p class="h2 fw-bold mb-0 text-primary-custom">
                            @if(isset($products) && $products->first())
                                ${{ number_format($products->first()->sale_price, 2) }}
                            @else
                                $0.00
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Products Section -->
<div class="container py-5" id="products-section">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5">
        <div>
            <span class="text-primary-custom text-uppercase fw-bold tracking-widest small">Catálogo</span>
            <h2 class="fw-bold display-5 mt-2">{{ $config->products_section_title ?? 'Nuevos Productos' }}</h2>
        </div>
        <a href="{{ route('tenant.shop.products') }}" class="btn btn-outline-dark rounded-0 px-4 py-2 text-uppercase tracking-wide mt-3 mt-md-0">Ver Todo <i class="fas fa-arrow-right ms-2"></i></a>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100 border-0 shadow-sm product-card rounded-0 overflow-hidden">
                    <div class="position-relative overflow-hidden image-wrapper bg-light" style="min-height: 300px;">
                        <a href="{{ route('tenant.shop.show', $product->id) }}">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top object-fit-cover transition-transform w-100 h-100" alt="{{ $product->name }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 text-secondary">
                                    <i class="fas fa-image fa-3x opacity-25"></i>
                                </div>
                            @endif
                        </a>
                        
                        <!-- Badges -->
                        <div class="position-absolute top-0 start-0 m-3 d-flex flex-column gap-2">
                            @if($product->stock <= 0)
                                <span class="badge bg-danger rounded-0 px-3 py-2 shadow-sm text-uppercase tracking-wide">Agotado</span>
                            @elseif($product->regular_price && $product->sale_price < $product->regular_price)
                                <span class="badge bg-dark rounded-0 px-3 py-2 shadow-sm text-uppercase tracking-wide">Oferta</span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons position-absolute bottom-0 start-0 w-100 p-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-white bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center action-btn" style="width: 45px; height: 45px;" onclick="addToCart(event, {{ $product->id }})" title="Añadir al Carrito">
                                <i class="fas fa-shopping-bag text-dark"></i>
                            </button>
                            <a href="{{ route('tenant.shop.show', $product->id) }}" class="btn btn-white bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center action-btn" style="width: 45px; height: 45px;" title="Ver Detalles">
                                <i class="fas fa-eye text-dark"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 text-center bg-white">
                        <div class="mb-2">
                            <small class="text-uppercase text-muted fw-bold tracking-wide" style="font-size: 0.7rem;">
                                {{ $product->category->name ?? 'General' }}
                            </small>
                        </div>
                        <h5 class="card-title fw-bold mb-2">
                            <a href="{{ route('tenant.shop.show', $product->id) }}" class="text-decoration-none text-dark stretched-link-custom product-title">
                                {{ $product->name }}
                            </a>
                        </h5>
                        <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
                            @if($product->regular_price && $product->regular_price > $product->sale_price)
                                <span class="text-muted text-decoration-line-through small">${{ number_format($product->regular_price, 2) }}</span>
                            @endif
                            <span class="fw-bold text-dark fs-5">${{ number_format($product->sale_price, 2) }}</span>
                        </div>
                        
                        <!-- Add to Cart (always visible) -->
                        <div class="mt-3">
                             <button class="btn btn-primary-custom w-100 rounded-0 text-uppercase tracking-wider small position-relative" style="z-index: 2;" onclick="addToCart(event, {{ $product->id }})">
                                <i class="fas fa-shopping-cart me-2"></i> Añadir
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Testimonials Section -->
@if($config->show_testimonials && isset($testimonials) && count($testimonials) > 0)
<div class="bg-light py-5 my-5">
    <div class="container py-4">
        <div class="text-center mb-5">
            <i class="fas fa-quote-left fa-3x text-primary-custom opacity-25 mb-3"></i>
            <h2 class="fw-bold display-6">{{ $config->testimonials_title ?? 'Lo que dicen nuestros clientes' }}</h2>
        </div>
        
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100 p-4 rounded-0">
                        <div class="d-flex text-warning mb-3">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $testimonial->rating)
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <p class="text-secondary mb-4">"{{ $testimonial->content }}"</p>
                        <div class="d-flex align-items-center mt-auto">
                            @if($testimonial->image_path)
                                <img src="{{ asset('storage/' . $testimonial->image_path) }}" class="rounded-circle me-3 object-fit-cover" width="40" height="40" alt="{{ $testimonial->name }}">
                            @else
                                <div class="bg-primary-custom rounded-circle text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                    {{ substr($testimonial->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <h6 class="fw-bold mb-0">{{ $testimonial->name }}</h6>
                                <small class="text-muted">{{ $testimonial->role ?? 'Cliente' }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Benefits Section -->
@if($config->show_benefits_section)
    <div class="container py-5 mb-5">
        <div class="row g-4 divide-x-lg">
            <div class="col-lg-4 text-center px-4">
                <i class="{{ $config->benefit_1_icon ?? 'fas fa-shipping-fast' }} fa-3x text-primary-custom mb-3"></i>
                <h5 class="fw-bold text-uppercase tracking-wide">{{ $config->benefit_1_title ?? 'Envío Rápido' }}</h5>
                <p class="text-muted">{{ $config->benefit_1_desc ?? 'Entregamos tus pedidos en tiempo récord.' }}</p>
            </div>
            <div class="col-lg-4 text-center px-4 border-start-lg border-end-lg">
                <i class="{{ $config->benefit_2_icon ?? 'fas fa-lock' }} fa-3x text-primary-custom mb-3"></i>
                <h5 class="fw-bold text-uppercase tracking-wide">{{ $config->benefit_2_title ?? 'Pago Seguro' }}</h5>
                <p class="text-muted">{{ $config->benefit_2_desc ?? 'Tus transacciones están protegidas.' }}</p>
            </div>
            <div class="col-lg-4 text-center px-4">
                <i class="{{ $config->benefit_3_icon ?? 'fas fa-headset' }} fa-3x text-primary-custom mb-3"></i>
                <h5 class="fw-bold text-uppercase tracking-wide">{{ $config->benefit_3_title ?? 'Soporte 24/7' }}</h5>
                <p class="text-muted">{{ $config->benefit_3_desc ?? 'Estamos aquí para ayudarte.' }}</p>
            </div>
        </div>
    </div>
@endif

<style>
    /* Hero Animations */
    .animate-zoom-in { animation: zoomIn 20s infinite alternate; }
    @keyframes zoomIn { from { transform: scale(1); } to { transform: scale(1.1); } }
    
    .animate-fade-in-up { animation: fadeInUp 0.8s ease-out forwards; opacity: 0; transform: translateY(20px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    .tracking-widest { letter-spacing: 0.2em; }
    .tracking-wide { letter-spacing: 0.1em; }
    
    .bg-dark-gradient { background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.6)); }
    .bg-gradient-to-t { background: linear-gradient(to top, rgba(0,0,0,0.8), transparent); }
    
    .hover-scale:hover { transform: scale(1.05); }
    .transition-all { transition: all 0.3s ease; }
    
    .divider { height: 3px; width: 60px; }
    
    /* Product Card */
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1)!important; }
    .image-wrapper img { transition: transform 0.5s ease; }
    .product-card:hover .image-wrapper img { transform: scale(1.05); }
    
    .action-buttons { transform: translateY(100%); transition: transform 0.3s ease; opacity: 0; }
    .product-card:hover .action-buttons { transform: translateY(0); opacity: 1; }
    
    .action-btn:hover { background-color: var(--primary-color) !important; color: white !important; }
    .action-btn:hover i { color: white !important; }
    
    .group-hover-scale:hover { transform: scale(1.1); }
    .duration-700 { transition-duration: 0.7s; }
</style>

@endsection