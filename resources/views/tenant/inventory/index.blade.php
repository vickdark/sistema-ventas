@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="inventory-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gestión de Inventario</h1>
            <p class="text-muted small">Control de existencias, ajustes manuales y seguimiento de movimientos.</p>
        </div>
        <div class="col-auto">
            <div class="btn-group">
                <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4 me-2">
                    <i class="fas fa-plus me-2"></i> Nuevo Producto
                </a>
                <a href="{{ route('purchases.create') }}" class="btn btn-outline-success rounded-pill px-4">
                    <i class="fas fa-cart-arrow-down me-2"></i> Registrar Compra
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 p-3 border-start border-primary border-5">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                        <i class="fas fa-box fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Total Productos</div>
                        <h4 class="mb-0 fw-bold">{{ \App\Models\Tenant\Product::count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 p-3 border-start border-danger border-5">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 text-danger p-3 rounded-circle me-3">
                        <i class="fas fa-exclamation-triangle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold text-uppercase">Bajo Stock</div>
                        <h4 class="mb-0 fw-bold text-danger">{{ \App\Models\Tenant\Product::whereColumn('stock', '<=', 'min_stock')->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary opacity-50 mb-3" role="status"></div>
                    <p class="text-muted small">Cargando inventario...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
