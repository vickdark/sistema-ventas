@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <a href="{{ route('supplier-payments.index') }}" class="btn btn-sm btn-light rounded-pill px-3 mb-2 shadow-sm border-0">
                <i class="fas fa-arrow-left me-1"></i> Volver a deudas
            </a>
            <h1 class="h3 mb-0 text-gray-800">Detalle de Deuda: Compra #{{ $purchase->nro_compra }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4 border-0">
                    <h5 class="fw-bold mb-0">Resumen Financiero</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Monto Total:</span>
                        <span class="fw-bold">${{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total Pagado:</span>
                        <span class="fw-bold text-success">${{ number_format($purchase->total_amount - $purchase->pending_amount, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold fs-5 text-dark">Pendiente:</span>
                        <span class="fw-extrabold fs-4 text-danger">${{ number_format($purchase->pending_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Información de Compra</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><span class="text-muted">Proveedor:</span> <span class="fw-bold d-block">{{ $purchase->supplier->name }}</span></li>
                        <li class="mb-2"><span class="text-muted">Fecha Compra:</span> <span class="d-block">{{ $purchase->purchase_date->format('d/m/Y') }}</span></li>
                        <li class="mb-2"><span class="text-muted">Vencimiento:</span> <span class="d-block text-danger FW-bold">{{ $purchase->due_date ? $purchase->due_date->format('d/m/Y') : 'N/A' }}</span></li>
                        <li><span class="text-muted">Voucher:</span> <span class="d-block">{{ $purchase->voucher }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Historial de Abonos</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 border-0">Fecha</th>
                                    <th class="py-3 border-0">Método</th>
                                    <th class="py-3 border-0">Notas</th>
                                    <th class="py-3 border-0 text-end">Monto</th>
                                    <th class="py-3 border-0 text-end" style="width: 100px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchase->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_date->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $payment->payment_method }}</span></td>
                                        <td class="small text-muted">{{ $payment->notes ?? '-' }}</td>
                                        <td class="text-end fw-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('supplier-payments.edit', $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill border-0 shadow-sm" title="Editar Abono">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted fst-italic">No se han registrado abonos aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Productos Comprados</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-sm small">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
