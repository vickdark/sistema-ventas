@extends('layouts.app')

@section('content')
<div class="container-fluid h-100 p-0">
    <div class="row h-100 g-0">
        <!-- Panel Izquierdo: Productos -->
        <div class="col-md-8 bg-light d-flex flex-column" style="height: calc(100vh - 65px);">
            <div class="p-3 bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="mb-0 fw-bold"><i class="fas fa-shopping-cart me-2 text-primary"></i> Punto de Venta</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group shadow-sm border-0 rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="productSearch" class="form-control border-0 py-2" placeholder="Buscar producto por nombre o código...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1 overflow-auto p-4">
                <div id="productsGrid" class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-3">
                    @foreach($products as $product)
                        <div class="col product-item" data-name="{{ strtolower($product->name) }}" data-code="{{ strtolower($product->code) }}">
                            <div class="card border-0 shadow-sm rounded-4 h-100 btn-add-product" 
                                 style="cursor: pointer;"
                                 data-id="{{ $product->id }}" 
                                 data-name="{{ $product->name }}" 
                                 data-price="{{ $product->sale_price }}"
                                 data-stock="{{ $product->stock }}">
                                <div class="card-body p-3 text-center">
                                    <div class="bg-primary bg-opacity-10 rounded-3 p-3 mb-2 mx-auto" style="width: 60px; height: 60px;">
                                        <i class="fas fa-box text-primary fs-4"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-truncate">{{ $product->name }}</h6>
                                    <div class="text-muted small mb-2">{{ $product->code }}</div>
                                    <div class="text-primary fw-bold fs-5">${{ number_format($product->sale_price, 2) }}</div>
                                    <div class="badge rounded-pill {{ $product->stock < 10 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $product->stock < 10 ? 'text-danger' : 'text-success' }} small">
                                        Stock: {{ $product->stock }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Carrito y Checkout -->
        <div class="col-md-4 bg-white border-start d-flex flex-column" style="height: calc(100vh - 65px);">
            <div class="p-3 border-bottom">
                <h5 class="fw-bold mb-3">Detalle de Venta #{{ $nextNroVenta }}</h5>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Cliente</label>
                    <select id="client_id" class="form-select rounded-3 border-light bg-light shadow-sm" required>
                        <option value="">Seleccione un cliente...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->nit_ci }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex-grow-1 overflow-auto p-2" id="cartItems">
                <div class="text-center py-4 text-muted opacity-50" id="emptyCart">
                    <i class="fas fa-shopping-basket fa-3x mb-2"></i>
                    <p class="small">El carrito está vacío</p>
                </div>
                <!-- Cart items will be injected here -->
            </div>

            <div class="p-3 bg-light border-top mt-auto">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted small">Subtotal</span>
                    <span class="fw-bold small" id="subtotalLabel">$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2 align-items-center">
                    <h5 class="mb-0 fw-bold">TOTAL</h5>
                    <h4 class="mb-0 fw-bold text-primary" id="totalLabel">$0.00</h4>
                </div>

                <div class="mb-2">
                    <div class="row g-1" id="paymentButtons">
                        <div class="col">
                            <input type="radio" class="btn-check" name="payment_type" id="pay_contado" value="CONTADO" checked>
                            <label class="btn btn-outline-primary w-100 rounded-3 py-1 px-1" for="pay_contado">
                                <i class="fas fa-money-bill-wave d-block mb-0 small"></i> <span style="font-size: 0.75rem;">Efectivo</span>
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="payment_type" id="pay_transfer" value="TRANSFERENCIA">
                            <label class="btn btn-outline-primary w-100 rounded-3 py-1 px-1" for="pay_transfer">
                                <i class="fas fa-mobile-alt d-block mb-0 small"></i> <span style="font-size: 0.75rem;">Transf.</span>
                            </label>
                        </div>
                        <div class="col">
                            <input type="radio" class="btn-check" name="payment_type" id="pay_credito" value="CREDITO">
                            <label class="btn btn-outline-primary w-100 rounded-3 py-1 px-1" for="pay_credito">
                                <i class="fas fa-calendar-alt d-block mb-0 small"></i> <span style="font-size: 0.75rem;">Crédito</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div id="creditDateSection" class="mb-2 d-none">
                    <label class="form-label small fw-bold mb-1" style="font-size: 0.7rem;">Fecha Límite (Opcional)</label>
                    <input type="date" id="credit_payment_date" class="form-control form-control-sm rounded-3 border-light bg-light" value="">
                </div>

                <div class="mb-3">
                    <input type="text" id="voucher" class="form-control form-control-sm rounded-3 border-light bg-light" placeholder="Referencia / Comprobante">
                </div>

                <button id="btnProcessSale" class="btn btn-primary w-100 py-2 rounded-pill fw-bold shadow-sm">
                    <i class="fas fa-check-circle me-2"></i> PROCESAR VENTA
                </button>
            </div>
        </div>
    </div>
</div>

<template id="cartItemTemplate">
    <div class="cart-item-row p-2 bg-white border rounded-3 shadow-sm mb-2">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-bold text-dark item-name small text-truncate" style="max-width: 150px;"></span>
            <button class="btn btn-link text-danger p-0 btn-remove-item"><i class="fas fa-times-circle small"></i></button>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div class="input-group input-group-sm" style="width: 80px;">
                <button class="btn btn-outline-secondary btn-minus px-2 py-0"><i class="fas fa-minus small"></i></button>
                <input type="text" class="form-control text-center border-secondary item-qty p-0" value="1" readonly style="font-size: 0.8rem;">
                <button class="btn btn-outline-secondary btn-plus px-2 py-0"><i class="fas fa-plus small"></i></button>
            </div>
            <div class="item-price-total fw-bold text-primary small"></div>
        </div>
    </div>
</template>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initSalesPOS({
            routes: {
                store: "{{ route('sales.store') }}",
                index: "{{ route('sales.index') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
