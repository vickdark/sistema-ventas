@if($products->count() > 0)
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-5">
        @foreach($products as $product)
            <div class="col">
                <div class="card h-100 border-0 rounded-0 bg-transparent group-hover-container">
                    <div class="position-relative overflow-hidden mb-3">
                        <a href="{{ route('tenant.shop.show', $product->id) }}" class="d-block">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-100 object-fit-cover transition-transform duration-500 hover-scale-105" alt="{{ $product->name }}" style="height: 350px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 350px;">
                                    <i class="fas fa-image fa-2x text-secondary opacity-25"></i>
                                </div>
                            @endif
                        </a>
                        @if($product->stock <= 0)
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-secondary rounded-0 px-3 py-2 text-uppercase tracking-wider small">Agotado</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-body p-0 text-center">
                        <div class="mb-2">
                            <small class="text-muted text-uppercase tracking-widest" style="font-size: 0.7rem;">
                                {{ $product->category->name ?? '' }}
                            </small>
                        </div>
                        <h5 class="card-title fw-normal mb-2">
                            <a href="{{ route('tenant.shop.show', $product->id) }}" class="text-decoration-none text-dark stretched-link">
                                {{ $product->name }}
                            </a>
                        </h5>
                        <p class="card-text text-dark fw-bold mb-3">
                            ${{ number_format($product->sale_price, 2) }}
                        </p>
                        
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
    
    <div class="mt-5 d-flex justify-content-center" id="pagination-container">
        {{ $products->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5 my-5">
        <div class="mb-4">
            <i class="fas fa-search fa-2x text-muted opacity-25"></i>
        </div>
        <h4 class="fw-normal text-uppercase tracking-wide mb-3">No hay resultados</h4>
        <p class="text-muted mb-4">No encontramos productos que coincidan con tu búsqueda.</p>
        <button class="btn btn-dark rounded-0 px-5 py-2 text-uppercase tracking-wide" onclick="resetFilters()">
            Ver todos
        </button>
    </div>
@endif

<style>
    .hover-scale-105:hover {
        transform: scale(1.05);
    }
    .duration-500 {
        transition-duration: 500ms;
    }
    .duration-300 {
        transition-duration: 300ms;
    }
    .transition-transform {
        transition-property: transform;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    .translate-y-100 {
        transform: translateY(100%);
    }
    .group-hover-container:hover .group-hover-visible {
        transform: translateY(0);
    }
    .group-hover-container:hover .hover-scale-105 {
        transform: scale(1.05);
    }
    .btn-white {
        background-color: white;
        border: 1px solid white;
        color: black;
    }
    .btn-white:hover {
        background-color: #f8f9fa;
        border-color: #f8f9fa;
    }
</style>
