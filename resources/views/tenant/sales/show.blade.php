@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 no-print">
        <div class="col">
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver al Listado
            </a>
        </div>
        <div class="col-auto">
            <a href="{{ route('credit-notes.create', ['sale_id' => $sale->id]) }}" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fas fa-undo me-2"></i> Procesar Devolución
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-5" id="sale-print-area">
            <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-4 border-0 text-center">
                    @if(tenant('logo'))
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
                        </div>
                    @else
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fas fa-receipt text-primary fs-2"></i>
                        </div>
                    @endif
                    <h4 class="fw-bold mb-1">{{ tenant('business_name') ?? tenant('id') }}</h4>
                    <p class="text-muted small mb-0">NIT: {{ tenant('tax_id') ?? 'N/A' }} | Nro. Venta: #{{ str_pad($sale->nro_venta, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                
                <div class="card-body p-4 border-top">
                    <!-- Detalle del Cliente -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2 text-center border-bottom pb-2">Información del Cliente</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Nombre:</span>
                            <span class="fw-bold">{{ $sale->client->name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">ID/Nit:</span>
                            <span class="fw-bold text-truncate">{{ $sale->client->nit_ci ?? '---' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Fecha:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d/m/Y h:i A') }}</span>
                        </div>
                    </div>

                    <!-- Detalle de Productos -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2 text-center border-bottom pb-2">Productos</h6>
                        <table class="table table-borderless table-sm mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>CANT.</th>
                                    <th>DESCRIPCIÓN</th>
                                    <th class="text-end">PRECIO</th>
                                    <th class="text-end">SUBTOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->items as $item)
                                    <tr>
                                        <td>{{ $item->quantity }}</td>
                                        <td class="text-truncate" style="max-width: 150px;">{{ $item->product->name }}</td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totales -->
                    <div class="bg-light p-3 rounded-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold">${{ number_format($sale->total_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                            <h5 class="mb-0 fw-bold">TOTAL PAGADO</h5>
                            <h4 class="mb-0 fw-bold text-primary">${{ number_format($sale->total_paid, 2) }}</h4>
                        </div>
                    </div>

                    <!-- Estado y Pago -->
                    <div class="row g-2 mb-4">
                        <div class="col">
                            <div class="p-2 border rounded-3 text-center">
                                <small class="text-muted d-block small mb-1">MÉTODO PAGO</small>
                                <span class="badge bg-primary rounded-pill">{{ $sale->payment_type }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="p-2 border rounded-3 text-center">
                                <small class="text-muted d-block small mb-1">ESTADO PAGO</small>
                                <span class="badge bg-{{ $sale->payment_status === 'PAGADO' ? 'success' : 'danger' }} rounded-pill">{{ $sale->payment_status }}</span>
                            </div>
                        </div>
                    </div>

                    @if($sale->voucher)
                    <div class="alert alert-light border-0 mb-4 py-2">
                        <small class="text-muted fw-bold d-block">REFERENCIA:</small>
                        <span>{{ $sale->voucher }}</span>
                    </div>
                    @endif

                    @if($sale->creditNotes->count() > 0)
                    <div class="mb-4">
                        <h6 class="text-uppercase text-danger fw-bold small mb-2 text-center border-bottom pb-2">Devoluciones / Notas de Crédito</h6>
                        @foreach($sale->creditNotes as $note)
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="{{ route('credit-notes.show', $note->id) }}" class="small fw-bold text-decoration-none">
                                    {{ $note->number }} {{ $note->status === 'void' ? '(Anulada)' : '' }}
                                </a>
                                <span class="text-danger fw-bold">-${{ number_format($note->total, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    @endif

                    <div class="d-grid no-print mt-4">
                        <button onclick="window.print()" class="btn btn-primary rounded-pill py-2 shadow-sm">
                            <i class="fas fa-print me-2"></i> Imprimir Recibo
                        </button>
                    </div>
                </div>
                
                <div class="card-footer bg-white border-top py-3 text-center text-muted small">
                    <p class="mb-0">{{ tenant('invoice_footer') ?? '¡Gracias por su compra!' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
