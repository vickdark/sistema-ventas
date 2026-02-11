@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Detalles del Producto</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden h-100">
                <div class="card-body p-0 text-center bg-light d-flex align-items-center justify-content-center" style="min-height: 300px;">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid shadow-sm rounded-3" style="max-height: 300px; object-fit: contain;">
                    @else
                        <div class="text-muted">
                            <i class="fas fa-box fa-5x mb-3 d-block"></i>
                            Sin imagen disponible
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white border-0 p-4 text-center">
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <p class="text-muted mb-0">Código: {{ $product->code }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Información General</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Categoría</label>
                                <span class="fw-medium">{{ $product->category->name }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Fecha de Entrada</label>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($product->entry_date)->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Precios</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Precio de Compra</label>
                                <span class="fw-bold text-danger fs-5">${{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Precio de Venta</label>
                                <span class="fw-bold text-success fs-5">${{ number_format($product->sale_price, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Inventario</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="text-muted d-block small">Stock Actual</label>
                                <span class="badge {{ $product->stock <= $product->min_stock ? 'bg-danger' : 'bg-primary' }} rounded-pill px-3 py-2 fs-6">
                                    {{ $product->stock }} unidades
                                </span>
                                @if($product->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" class="img-thumbnail" style="height: 60px;">
                                        <small class="text-muted d-block">Imagen actual</small>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted d-block small">Stock Mínimo</label>
                                <span class="fw-medium">{{ $product->min_stock }}</span>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="text-muted d-block small">Stock Máximo</label>
                                <span class="fw-medium">{{ $product->max_stock }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Descripción</h6>
                        <p class="text-secondary mb-0">
                            {{ $product->description ?: 'Sin descripción detallada.' }}
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-light border-0 px-4 py-3">
                    <small class="text-muted">Registrado por: {{ $product->user->name ?? 'Sistema' }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
