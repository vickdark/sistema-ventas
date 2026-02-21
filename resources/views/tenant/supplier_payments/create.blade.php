@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div id="supplier-payments-create-page" data-config='@json(["routes" => ["store" => route("supplier-payments.store"), "index" => route("supplier-payments.index")], "purchase" => $purchase])'></div>

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registrar Abono</h1>
        <a href="{{ route('supplier-payments.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-gray-600"></i> Volver
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4">
                <div class="card-header border-0 bg-white py-3 px-4 rounded-top-4">
                    <h6 class="m-0 font-weight-bold text-primary">Detalles del Pago</h6>
                </div>
                <div class="card-body p-4">
                    @if($purchase)
                        <div class="mb-4 bg-light p-3 rounded-4">
                            <div class="row text-center">
                                <div class="col">
                                    <span class="d-block text-muted small uppercase fw-bold">Proveedor</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $purchase->supplier->name }}</span>
                                </div>
                                <div class="col border-start">
                                    <span class="d-block text-muted small uppercase fw-bold">Nro. Compra</span>
                                    <span class="fw-bold fs-5 text-dark">{{ $purchase->nro_compra }}</span>
                                </div>
                                <div class="col border-start">
                                    <span class="d-block text-muted small uppercase fw-bold">Deuda Total</span>
                                    <span class="fw-bold fs-5 text-dark">${{ number_format($purchase->total_amount, 2) }}</span>
                                </div>
                                <div class="col border-start">
                                    <span class="d-block text-muted small uppercase fw-bold">Pendiente</span>
                                    <span class="fw-bold fs-5 text-danger">${{ number_format($purchase->pending_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            No se ha seleccionado una compra válida. Por favor regrese e intente nuevamente.
                        </div>
                    @endif

                    @if($purchase)
                    <form id="paymentForm">
                        <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                        
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Monto del Abono</label>
                            <div class="input-group shadow-sm rounded-4 overflow-hidden">
                                <span class="input-group-text border-0 bg-white"><i class="fas fa-dollar-sign text-muted"></i></span>
                                <input type="number" step="0.01" id="payment_amount" name="amount" class="form-control border-0 py-3 fs-5" placeholder="0.00" value="{{ $purchase->pending_amount }}" max="{{ $purchase->pending_amount }}" required>
                            </div>
                            <small class="text-muted ms-2">Monto máximo: ${{ number_format($purchase->pending_amount, 2) }}</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Fecha de Pago</label>
                                <input type="date" name="payment_date" class="form-control border-0 shadow-sm rounded-4 py-2" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Método de Pago</label>
                                <select name="payment_method" class="form-select border-0 shadow-sm rounded-4 py-2" required>
                                    <option value="EFECTIVO">Efectivo</option>
                                    <option value="TRANSFERENCIA">Transferencia</option>
                                    <option value="CHEQUE">Cheque</option>
                                    <option value="DEPOSITO">Depósito</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted">Notas / Referencia</label>
                            <textarea name="notes" class="form-control border-0 shadow-sm rounded-4 p-3" rows="3" placeholder="Nro de transferencia, observaciones..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-3 rounded-4 fw-bold shadow-soft border-0" id="btnSavePayment">
                                <i class="fas fa-check-circle me-2"></i> CONFIRMAR PAGO
                            </button>
                            <a href="{{ route('supplier-payments.index') }}" class="btn btn-light py-3 rounded-4 fw-bold border-0 text-muted">
                                CANCELAR
                            </a>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection