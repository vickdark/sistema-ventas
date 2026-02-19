@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Nota de Crédito #{{ $note->number }}</h1>
            <p class="text-muted">Referencia: Venta #{{ $note->sale->nro_venta }} | Fecha: {{ $note->created_at->format('d/m/Y H:i') }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('credit-notes.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-print me-2"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-light py-3 px-4">
                    <h6 class="mb-0 fw-bold">Productos Devueltos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">Producto</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end px-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($note->items as $item)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">{{ $item->product->name }}</div>
                                        <div class="text-muted small">Cod: {{ $item->product->code }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                    <td class="text-end px-4 fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3">TOTAL DEVUELTO:</td>
                                    <td class="text-end px-4 fw-bold text-primary py-3 h5 mb-0">
                                        ${{ number_format($note->total, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Información General</h6>
                    
                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase fw-bold">Motivo</label>
                        <span>{{ $note->reason }}</span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase fw-bold">Estado</label>
                        <span class="badge bg-{{ $note->status === 'active' ? 'success' : 'danger' }} rounded-pill">
                            {{ $note->status === 'active' ? 'ACTIVA' : 'ANULADA' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <label class="small text-muted d-block text-uppercase fw-bold">Generada por</label>
                        <span>{{ $note->user->name }}</span>
                    </div>

                    <hr>

                    <h6 class="fw-bold mb-3">Cliente</h6>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            <i class="fas fa-user border-0"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $note->sale->client->name ?? 'Venta Rápida' }}</div>
                            <div class="text-muted small">{{ $note->sale->client->email ?? '' }}</div>
                        </div>
                    </div>

                    <a href="{{ route('sales.show', $note->sale_id) }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 mt-2">
                        Ver Venta Original
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .app-header, .app-sidebar, .app-footer, .col-auto { display: none !important; }
    .container-fluid { margin: 0; padding: 0; }
    .card { border: none !important; shadow: none !important; }
}
</style>
@endsection
