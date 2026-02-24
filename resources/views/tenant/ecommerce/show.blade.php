@extends('tenant.ecommerce.layout')

@section('content')

<!-- Breadcrumb -->
<div class="bg-white border-bottom py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 bg-transparent p-0 small text-uppercase tracking-wide">
                <li class="breadcrumb-item"><a href="{{ route('tenant.shop.index') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('tenant.shop.products') }}" class="text-decoration-none text-muted">Catálogo</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('tenant.shop.products', ['category' => $product->category->id]) }}" class="text-decoration-none text-muted">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active text-dark fw-medium" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        <!-- Product Images -->
        <div class="col-lg-6">
            <div class="position-relative cursor-pointer group-hover-container" data-bs-toggle="modal" data-bs-target="#imageModal">
                @if($product->stock <= 0)
                    <div class="position-absolute top-0 start-0 m-3 z-1">
                        <span class="badge bg-secondary rounded-0 px-3 py-2 text-uppercase tracking-wider">Agotado</span>
                    </div>
                @endif
                
                @if($product->image)
                    <div class="bg-white position-relative overflow-hidden">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-100 object-fit-contain transition-transform duration-500 hover-scale-105" style="max-height: 600px;">
                        <div class="position-absolute top-50 start-50 translate-middle bg-white bg-opacity-75 text-dark rounded-0 p-3 opacity-0 group-hover-visible transition-opacity duration-300">
                            <i class="fas fa-search-plus fa-lg"></i>
                        </div>
                    </div>
                @else
                    <div class="ratio ratio-1x1 bg-light d-flex align-items-center justify-content-center text-secondary">
                        <i class="fas fa-image fa-5x opacity-25"></i>
                    </div>
                @endif
            </div>
            <div class="text-center mt-3 text-muted small text-uppercase tracking-wide d-lg-none">
                <i class="fas fa-search-plus me-1"></i> Toca para ampliar
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="ps-lg-5 d-flex flex-column h-100">
                <div class="mb-3">
                    <span class="text-uppercase text-secondary small tracking-widest border-bottom border-dark pb-1">{{ $product->category->name ?? 'General' }}</span>
                </div>
                
                <h1 class="display-4 fw-bold text-dark mb-4 text-uppercase tracking-tight">{{ $product->name }}</h1>
                
                <div class="d-flex align-items-center gap-4 mb-5">
                    <span class="display-5 fw-normal text-dark">${{ number_format($product->sale_price, 2) }}</span>
                    @if($product->regular_price && $product->regular_price > $product->sale_price)
                        <span class="text-decoration-line-through text-muted fs-4">${{ number_format($product->regular_price, 2) }}</span>
                    @endif
                </div>

                <div class="mb-5">
                    <p class="text-secondary lead fs-6 lh-lg">{{ $product->description }}</p>
                </div>

                <div class="d-flex align-items-center gap-3 mb-5 text-uppercase small tracking-wide">
                    <div class="d-flex align-items-center gap-2 text-muted">
                        <i class="fas fa-barcode"></i> SKU: {{ $product->code ?? 'N/A' }}
                    </div>
                    @if($product->stock > 0)
                        <div class="d-flex align-items-center gap-2 text-success">
                            <i class="fas fa-check-circle"></i> Disponible
                        </div>
                    @endif
                </div>

                <div class="mt-auto pt-5 border-top border-dark">
                    <div class="row g-4 align-items-end">
                        <div class="col-4">
                            <label class="form-label text-muted small fw-bold text-uppercase tracking-wide mb-2">Cantidad</label>
                            <div class="input-group border border-dark">
                                <button class="btn btn-link text-dark text-decoration-none px-3" type="button" onclick="updateQty(-1)">-</button>
                                <input type="number" id="quantity" class="form-control text-center border-0 bg-transparent fw-bold" value="1" min="1" max="{{ $product->stock }}">
                                <button class="btn btn-link text-dark text-decoration-none px-3" type="button" onclick="updateQty(1)">+</button>
                            </div>
                        </div>
                        <div class="col-8">
                            <button class="btn btn-dark w-100 py-3 rounded-0 text-uppercase tracking-widest fw-bold hover-scale" onclick="addCurrentProductToCart()" {{ $product->stock < 1 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-bag me-2"></i> Añadir al Carrito
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Info Tabs -->
    <div class="row mt-5 pt-5">
        <div class="col-12">
            <ul class="nav nav-tabs nav-fill" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Descripción</button>
                </li>
            </ul>
            <div class="tab-content p-4 border border-top-0 rounded-bottom bg-white shadow-sm" id="productTabContent">
                <div class="tab-pane fade show active" id="desc" role="tabpanel">
                    <p class="text-secondary mb-0">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 text-center position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3" data-bs-dismiss="modal" aria-label="Close"></button>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
                @endif
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    function updateQty(change) {
        const input = document.getElementById('quantity');
        const max = parseInt(input.getAttribute('max')) || 100;
        let newValue = parseInt(input.value) + change;
        
        if (newValue >= 1 && newValue <= max) {
            input.value = newValue;
        }
    }

    function addCurrentProductToCart() {
        const quantity = document.getElementById('quantity').value;
        if(quantity < 1) {
            alert('La cantidad debe ser al menos 1');
            return;
        }
        // Use global addToCart if available
        if (typeof addToCart === 'function') {
            addToCart({{ $product->id }}, quantity);
        } else {
            console.error('addToCart function not found');
        }
    }
</script>
@endsection

<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .image-hover-overlay {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .image-container:hover .image-hover-overlay {
        opacity: 1;
    }
    .transition-transform-img {
        transition: transform 0.3s ease;
    }
    .image-container:hover .transition-transform-img {
        transform: scale(1.02);
    }
    .tracking-wide {
        letter-spacing: 0.1em;
    }
    .transition-transform:active {
        transform: scale(0.98);
    }
    /* Hide number input arrows */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>

@endsection