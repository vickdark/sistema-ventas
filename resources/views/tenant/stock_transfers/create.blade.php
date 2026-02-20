@extends('layouts.app')

@section('content')

{{-- Configuración de Página para PageLoader.js --}}
<div id="stock-transfers-create-page" data-config='@json($config)'></div>

<div class="container-fluid p-0 pos-container">
    <div class="row h-100 g-0 position-relative">
        <!-- Panel de Productos (Izquierda) -->
        <div class="col-lg-8 product-panel">
            <!-- Buscador Superior -->
            <div class="search-header">
                <div class="position-relative flex-grow-1">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted pos-search-icon"></i>
                    <input type="text" id="productSearch" class="form-control pos-search-input py-3" placeholder="Buscar productos para trasladar...">
                </div>
                <div class="ms-4 d-none d-lg-flex gap-2">
                    <span class="badge bg-white text-slate-500 border py-2 px-3 rounded-pill text-muted small">
                         <i class="fas fa-keyboard me-2 opacity-50"></i> F2: Buscar
                    </span>
                </div>
            </div>

            <!-- Carrusel de Filtros -->
            <div class="categories-bar">
                <button class="category-btn active">Todos los Productos</button>
                @php 
                    $categories = $products->pluck('category.name')->unique()->filter();
                @endphp
                @foreach($categories as $cat)
                    <button class="category-btn">{{ $cat }}</button>
                @endforeach
            </div>

            <!-- Listado de Productos en Swiper -->
            <div class="product-grid-scroll">
                <div class="swiper swiper-products">
                    <div id="productsGrid" class="swiper-wrapper">
                        @foreach($products as $product)
                            <div class="swiper-slide product-item" 
                                 data-name="{{ strtolower($product->name) }}" 
                                 data-code="{{ strtolower($product->code) }}"
                                 data-category="{{ strtolower($product->category->name ?? '') }}">
                                <div class="card modern-product-card btn-add-product" 
                                     data-id="{{ $product->id }}" 
                                     data-name="{{ $product->name }}" 
                                     data-stock="{{ $product->stock }}"
                                     data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                    <div class="product-card-img">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-img-pos">
                                        @else
                                            <i class="fas fa-box fa-3x text-slate-200 pos-empty-box-icon"></i>
                                        @endif
                                        <span class="stock-badge {{ $product->stock < 10 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-emerald-50 text-emerald-600' }}">
                                            {{ $product->stock }} disp.
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-slate-700 mb-1 text-truncate">{{ $product->name }}</h6>
                                        <p class="text-muted small mb-3 letter-spacing-1">#{{ $product->code }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-indigo-50 text-indigo-700 rounded-pill px-3">En Stock</span>
                                            <div class="bg-indigo-50 p-2 rounded-lg pos-add-btn">
                                                <i class="fas fa-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="swiper-pagination mt-4"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>

        <!-- Panel de Traslado (Derecha) -->
        <div class="col-lg-4 cart-sidebar" id="cartSidebar">
            <div class="cart-header">
                <div>
                    <h5 class="fw-extrabold text-slate-800 mb-0">Nuevo Envío</h5>
                    <span class="text-muted small">Traslado #{{ str_pad($nextNroTraslado, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold flex-shrink-0" id="btnClearCart">
                        <i class="fas fa-trash-alt me-2"></i>Vaciar
                    </button>
                    <button class="btn btn-light d-lg-none rounded-pill" id="btnCloseCart">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 bg-slate-50" style="background: #f8fafc;">
                <div class="mb-0">
                    <label class="form-label small fw-bold text-slate-500 mb-1">SUCURSAL DESTINO</label>
                    <select id="destination_branch_id" class="form-select border-0 shadow-sm rounded-4 py-2 px-3">
                        <option value="">Seleccione destino...</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="cart-items-scroll flex-grow-1" id="cartItems">
                <div class="text-center py-5 text-muted opacity-40" id="emptyCart">
                    <i class="fas fa-truck-ramp-box fa-4x mb-3"></i>
                    <h6 class="fw-bold">Envío vacío</h6>
                    <p class="small">Seleccione productos para trasladar</p>
                </div>
            </div>

            <div class="checkout-footer">
                <div class="mb-4">
                    <label class="form-label small fw-bold text-slate-500 mb-1 caps-letter-spacing">OBSERVACIONES</label>
                    <textarea id="notes" class="form-control border-0 bg-slate-100 rounded-4 p-3" rows="3" placeholder="Motivo del traslado, conductor, etc..." style="background: #f1f5f9;"></textarea>
                </div>

                <div class="bg-indigo-50 p-3 rounded-4 mb-4 border border-indigo-100 d-flex justify-content-between align-items-center">
                    <span class="text-indigo-700 fw-bold small">Items para envío:</span>
                    <span class="h4 fw-extrabold text-indigo-600 mb-0 cart-count">0</span>
                </div>

                <button id="btnSaveTransfer" class="btn-pay-action d-flex align-items-center justify-content-center" disabled>
                    <i class="fas fa-paper-plane me-2"></i> CONFIRMAR ENVÍO
                </button>
            </div>
        </div>
    </div>

    <!-- Botón Flotante para Móvil -->
    <button class="btn-floating-cart d-lg-none" id="btnToggleCart">
        <i class="fas fa-truck-moving"></i>
        <span class="badge bg-danger rounded-pill cart-count">0</span>
    </button>
</div>

<template id="cartItemTemplate">
    <div class="cart-item-modern shadow-sm border-start border-4 border-indigo-400">
        <div class="d-flex align-items-center mb-2">
            <div class="cart-item-img-container me-3">
                <div class="cart-item-icon-placeholder rounded-3 bg-light d-flex align-items-center justify-content-center cart-item-placeholder">
                    <i class="fas fa-box text-muted small"></i>
                </div>
            </div>
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="fw-bold text-slate-800 item-name small text-truncate pe-2"></span>
                    <button class="btn btn-link text-danger p-0 btn-remove-item border-0 outline-0" title="Eliminar">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="qty-control" style="background: white; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; align-items: center; padding: 2px;">
                <button class="btn-qty btn-minus border-0 bg-transparent text-muted px-2"><i class="fas fa-minus small"></i></button>
                <input type="text" class="form-control form-control-sm text-center border-0 bg-transparent item-qty fw-bold p-0" value="1" readonly style="width: 25px; font-size: 0.8rem;">
                <button class="btn-qty btn-plus border-0 bg-transparent text-muted px-2"><i class="fas fa-plus small"></i></button>
            </div>
            <div class="text-end">
                <div class="text-muted small item-stock-limit" style="font-size: 0.7rem;"></div>
                <div class="fw-bold text-indigo-600 small">ENVIANDO</div>
            </div>
        </div>
    </div>
</template>
@endsection
