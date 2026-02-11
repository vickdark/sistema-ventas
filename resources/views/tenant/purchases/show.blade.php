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
        @php
            // Obtener todos los proveedores únicos de los productos comprados
            $allSuppliers = $purchase->items->flatMap(function($item) {
                return $item->product->suppliers;
            })->unique('id');
            
            // Si por alguna razón no hay proveedores en los productos, usar el proveedor principal de la compra
            if ($allSuppliers->isEmpty() && $purchase->supplier) {
                $allSuppliers = collect([$purchase->supplier]);
            }
        @endphp

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
                    <h2 class="text-primary fw-bold mb-3">${{ number_format($purchase->total, 2) }}</h2>
                    <p class="text-muted small mb-0">Comprobante: <strong>{{ $purchase->voucher }}</strong></p>
                    <p class="text-muted small mb-0">Fecha: {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Proveedores involucrados</h6>
                        <div class="row g-3">
                            @foreach($allSuppliers as $supplier)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 border rounded-3 bg-light bg-opacity-50">
                                        <div class="bg-white rounded-circle shadow-sm p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-truck text-primary small"></i>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="mb-0 text-truncate fw-bold">{{ $supplier->name }}</h6>
                                            <p class="text-muted small mb-0 text-truncate">{{ $supplier->company }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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

        <div class="col-12">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 px-4">
                    <h5 class="mb-0 fw-bold">Productos Comprados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 border-0">Producto</th>
                                    <th class="py-3 border-0 text-center">Cantidad</th>
                                    <th class="py-3 border-0 text-center">Precio Unit.</th>
                                    <th class="px-4 py-3 border-0 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold">{{ $item->product->name }}</div>
                                        <div class="small text-muted">{{ $item->product->code }}</div>
                                    </td>
                                    <td class="py-3 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 text-center">${{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-3 text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="3" class="px-4 py-3 text-end">TOTAL</td>
                                    <td class="px-4 py-3 text-end text-primary fs-5">${{ number_format($purchase->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03) !important;
    }
    .rounded-4 {
        border-radius: 1rem !important;
    }
</style>
@endsection
