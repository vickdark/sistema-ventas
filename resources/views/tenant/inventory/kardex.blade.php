@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Kardex de Producto</h1>
            <p class="text-muted small">Historial detallado de movimientos: {{ $product->name }} ({{ $product->code }})</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver al Inventario
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded-4 mb-3" style="max-height: 150px;">
                    @else
                        <div class="bg-light rounded-4 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px;">
                            <i class="fas fa-box text-muted fs-1"></i>
                        </div>
                    @endif
                    <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                    <span class="badge bg-primary rounded-pill mb-3">{{ $product->category->name ?? 'S/C' }}</span>
                    
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3">
                                <small class="text-muted d-block small">Stock Actual</small>
                                <h5 class="mb-0 fw-bold">{{ $product->stock }}</h5>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded-3">
                                <small class="text-muted d-block small">Stock Mínimo</small>
                                <h5 class="mb-0 fw-bold text-danger">{{ $product->min_stock }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Historial de Movimientos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">Fecha</th>
                                    <th>Tipo</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-center">Ant.</th>
                                    <th class="text-center">Nuevo</th>
                                    <th>Motivo</th>
                                    <th class="px-4">Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $mov)
                                <tr>
                                    <td class="px-4 small">
                                        {{ $mov->created_at->format('d/m/Y') }}<br>
                                        <span class="text-muted opacity-50">{{ $mov->created_at->format('H:i') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $mov->type === 'input' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $mov->type === 'input' ? 'success' : 'danger' }} rounded-pill px-3">
                                            {{ $mov->type === 'input' ? 'ENTRADA' : 'SALIDA' }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-bold">{{ $mov->quantity }}</td>
                                    <td class="text-center text-muted">{{ $mov->prev_stock }}</td>
                                    <td class="text-center fw-bold text-primary">{{ $mov->new_stock }}</td>
                                    <td>
                                        <div class="small fw-bold">{{ $mov->reason }}</div>
                                        @if($mov->description)
                                            <div class="x-small text-muted">{{ $mov->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 small">{{ $mov->user->name ?? 'Sistema' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No hay movimientos registrados para este producto.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($movements->hasPages())
                <div class="card-footer bg-white py-3 px-4">
                    {{ $movements->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
