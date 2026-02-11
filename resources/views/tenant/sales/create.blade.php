@extends('layouts.app')

@section('content')
@push('styles')
    @vite(['resources/css/pages/pos.css'])
@endpush

<div class="container-fluid p-0 pos-container">
    <div class="row h-100 g-0 position-relative">
        <!-- Dashboard de Ventas (Izquierda) -->
        <div class="col-lg-8 product-panel">
            <!-- Buscador Superior -->
            <div class="search-header">
                <div class="position-relative flex-grow-1">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="z-index: 10;"></i>
                    <input type="text" id="productSearch" class="form-control pos-search-input py-3" placeholder="Buscar por nombre, código de barras o categoría...">
                </div>
                <div class="ms-4 d-none d-lg-flex gap-2">
                    <span class="badge bg-white text-slate-500 border py-2 px-3 rounded-pill text-muted small">
                         <i class="fas fa-keyboard me-2 opacity-50"></i> F2: Buscar
                    </span>
                    <span class="badge bg-white text-slate-500 border py-2 px-3 rounded-pill text-muted small">
                         <i class="fas fa-bolt me-2 opacity-50"></i> F4: Cobrar
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

            <!-- Listado de Productos en Carousel -->
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
                                     data-price="{{ $product->sale_price }}"
                                     data-stock="{{ $product->stock }}"
                                     data-image="{{ $product->image ? asset('storage/' . $product->image) : '' }}">
                                    <div class="product-card-img">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-img-pos">
                                        @else
                                            <i class="fas fa-box fa-3x text-slate-200" style="color: #e2e8f0;"> z-index: 1;"></i>
                                        @endif
                                        <span class="stock-badge {{ $product->stock < 10 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-emerald-50 text-emerald-600' }}" 
                                              style="{{ $product->stock >= 10 ? 'background: #ecfdf5; color: #059669;' : '' }}; z-index: 2;">
                                            {{ $product->stock }} disp.
                                        </span>
                                    </div>
                                    <div class="card-body p-3">
                                        <h6 class="fw-bold text-slate-700 mb-1 text-truncate">{{ $product->name }}</h6>
                                        <p class="text-muted small mb-3 letter-spacing-1">#{{ $product->code }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h5 fw-extrabold text-indigo-600 mb-0" style="color: #4f46e5;">${{ number_format($product->sale_price, 2) }}</span>
                                            <div class="bg-indigo-50 p-2 rounded-lg" style="background: #eef2ff; color: #4f46e5; border-radius: 10px;">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Controles del Swiper -->
                    <div class="swiper-pagination mt-4"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>

        <!-- Panel de Carrito (Derecha) -->
        <div class="col-lg-4 cart-sidebar" id="cartSidebar">
            <div class="cart-header">
                <div>
                    <h5 class="fw-extrabold text-slate-800 mb-0">Detalle de Venta</h5>
                    <span class="text-muted small">Ticket #{{ str_pad($nextNroVenta, 6, '0', STR_PAD_LEFT) }}</span>
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

            <div class="p-3 bg-slate-50" style="background: #f8fafc;">
                <label class="form-label small fw-bold text-slate-500 mb-1">CLIENTE</label>
                <div class="d-flex gap-2">
                    <div class="flex-grow-1">
                        <select id="client_id" class="form-select border-0 shadow-sm" style="border-radius: 12px; height: 45px;">
                            <option value="">Consumidor Final</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-white border-0 shadow-sm" id="btnOpenQuickClient" style="border-radius: 12px; width: 45px;" title="Nuevo Cliente">
                        <i class="fas fa-plus text-indigo-600"></i>
                    </button>
                </div>
            </div>

            <div class="cart-items-scroll" id="cartItems">
                <div class="text-center py-5 text-muted opacity-40" id="emptyCart">
                    <i class="fas fa-shopping-basket fa-4x mb-3"></i>
                    <h6 class="fw-bold">El resumen está vacío</h6>
                    <p class="small">Añade productos para continuar</p>
                </div>
            </div>

            <div class="checkout-footer">
                <div class="total-display">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-slate-500 fw-bold">Subtotal</span>
                        <span class="fw-bold text-slate-800" id="subtotalLabel">$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <h3 class="fw-extrabold text-slate-900 mb-0">Total</h3>
                        <h2 class="fw-extrabold text-indigo-600 mb-0" id="totalLabel" style="color: #4f46e5;">$0.00</h2>
                    </div>
                </div>

                <div class="row g-2 mb-4">
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="payment_type" id="pay_contado" value="CONTADO" checked>
                        <label class="btn btn-outline-indigo pay-cash w-100 py-3 rounded-4 border-2" for="pay_contado">
                            <i class="fas fa-money-bill-wave d-block mb-1"></i> <span class="small fw-bold">Efectivo</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="payment_type" id="pay_transfer" value="TRANSFERENCIA">
                        <label class="btn btn-outline-indigo pay-bank w-100 py-3 rounded-4 border-2" for="pay_transfer">
                            <i class="fas fa-university d-block mb-1"></i> <span class="small fw-bold">Banco</span>
                        </label>
                    </div>
                    <div class="col-4">
                        <input type="radio" class="btn-check" name="payment_type" id="pay_credito" value="CREDITO">
                        <label class="btn btn-outline-indigo pay-credit w-100 py-3 rounded-4 border-2" for="pay_credito">
                            <i class="fas fa-clock d-block mb-1"></i> <span class="small fw-bold">Crédito</span>
                        </label>
                    </div>
                </div>

                <!-- Calculadora de Cambio para Efectivo -->
                <div id="cashCalculation" class="bg-indigo-50 p-3 rounded-4 mb-4 border border-indigo-100" style="background: #f5f3ff; border: 1px solid #ddd6fe;">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-indigo-700">Paga con:</label>
                            <input type="number" id="received_amount" class="form-control border-0 shadow-sm" placeholder="0.00" style="border-radius: 10px;">
                        </div>
                        <div class="col-6 text-end">
                            <label class="form-label small fw-bold text-indigo-700">Cambio:</label>
                            <div class="h4 fw-extrabold text-indigo-600 mb-0" id="changeLabel">$0.00</div>
                        </div>
                    </div>
                </div>

                <!-- Fecha crédito (condicional) -->
                <div id="creditDateSection" class="mb-4 d-none">
                    <label class="form-label small fw-bold">Fecha Límite</label>
                    <input type="date" id="credit_payment_date" class="form-control rounded-4 shadow-sm" value="">
                </div>

                <div class="mb-4">
                    <input type="text" id="voucher" class="form-control border-0 bg-slate-100 py-3 px-4" placeholder="Referencia / Comprobante" style="background: #f1f5f9; border-radius: 12px;">
                </div>

                <button id="btnProcessSale" class="btn-pay-action" disabled>
                    <i class="fas fa-check-circle me-2"></i> PROCESAR PAGO
                </button>
            </div>
        </div>
    </div>

    <!-- Botón Flotante para Carrito en Móvil -->
    <button class="btn-floating-cart d-lg-none" id="btnToggleCart">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge bg-danger rounded-pill cart-count">0</span>
    </button>
</div>

<!-- Modal Cliente Rápido -->
<div class="modal fade" id="quickClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Nuevo Cliente Rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="quickClientForm">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nombre Completo</label>
                        <input type="text" name="name" class="form-control rounded-3 border-light bg-light" placeholder="Ej. Juan Pérez" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NIT / CI</label>
                        <input type="text" name="nit_ci" class="form-control rounded-3 border-light bg-light" placeholder="Ej. 1234567" required title="NIT o Cédula de Identidad">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Celular (Opcional)</label>
                        <input type="text" name="phone" class="form-control rounded-3 border-light bg-light" placeholder="Ej. 70000000">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Correo (Opcional)</label>
                        <input type="email" name="email" class="form-control rounded-3 border-light bg-light" placeholder="cliente@correo.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnSaveQuickClient" class="btn btn-primary rounded-pill px-4 fw-bold">
                    Guardar Cliente
                </button>
            </div>
        </div>
    </div>
</div>

<template id="cartItemTemplate">
    <div class="cart-item-modern shadow-sm">
        <div class="d-flex align-items-center mb-2">
            <div class="cart-item-img-container me-3">
                <img src="" alt="" class="cart-item-img rounded-3 d-none" style="width: 45px; height: 45px; object-fit: cover;">
                <div class="cart-item-icon-placeholder rounded-3 bg-light d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
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
                <div class="text-muted small item-price-unit" style="font-size: 0.65rem;"></div>
                <div class="item-price-total fw-extrabold text-indigo-600"></div>
            </div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Estilos específicos para el POS
        document.body.classList.add('pos-page');

        // Minimizar sidebar automáticamente para el POS
        if (window.innerWidth > 991) {
            document.body.classList.add('sidebar-mini');
        }

        if (typeof initSalesPOS === 'function') {
            initSalesPOS({
                routes: {
                    store: "{{ route('sales.store') }}",
                    index: "{{ route('sales.index') }}",
                    clients_store: "{{ route('clients.store') }}"
                },
                tokens: {
                    csrf: "{{ csrf_token() }}"
                }
            });
        }
    });
</script>
@endsection
