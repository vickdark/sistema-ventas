@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Detalles de Compra #{{ $purchase->nro_compra }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-edit me-2"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 text-center">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center">
                    @if(tenant('logo'))
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" class="img-fluid rounded shadow-sm" style="max-height: 80px;">
                        </div>
                    @else
                        <div class="rounded-circle bg-primary bg-opacity-10 p-4 mb-3">
                            <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ tenant('business_name') ?? tenant('id') }}</h5>
                    <p class="text-muted small mb-3">NIT: {{ tenant('tax_id') ?? 'N/A' }}</p>
                    <hr class="w-100 my-2">
                    <h5 class="fw-bold mb-1 mt-2">Monto Total</h5>
                    <h2 class="text-primary fw-bold mb-3">${{ number_format($purchase->quantity * $purchase->price, 2) }}</h2>
                    <p class="text-muted small mb-0">Comprobante: <strong>{{ $purchase->voucher }}</strong></p>
                    <p class="text-muted small mb-0">Fecha: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Proveedor</h6>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="fas fa-truck text-secondary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $purchase->supplier->name }}</h5>
                                <p class="text-muted mb-0">{{ $purchase->supplier->company }} - {{ $purchase->supplier->phone }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Producto Comprado</h6>
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded p-3 me-3">
                                <i class="fas fa-box text-secondary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ $purchase->product->name }}</h5>
                                <p class="text-muted mb-0">Código: {{ $purchase->product->code }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Cantidad</h6>
                            <div class="fs-5 fw-bold">{{ $purchase->quantity }} unidades</div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3">Precio Unitario</h6>
                            <div class="fs-5 fw-bold text-success">${{ number_format($purchase->price, 2) }}</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Información de Registro</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-muted d-block small">Registrado por</label>
                                <span class="fw-medium">{{ $purchase->user->name ?? 'Sistema' }}</span>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted d-block small">Fecha de Sistema</label>
                                <span class="fw-medium">{{ $purchase->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
