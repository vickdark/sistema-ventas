@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div id="supplier-payments-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cuentas por Pagar</h1>
            <p class="text-muted small">Control de deudas y abonos a proveedores.</p>
        </div>
    </div>

    <!-- Resumen de Deuda (Opcional, se puede expandir luego) -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-soft h-100 py-2 border-start border-danger border-4 rounded-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pendiente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingLabel">$0.00</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Abono -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Registrar Pago a Proveedor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="paymentForm">
                    <input type="hidden" id="modal_purchase_id">
                    
                    <div class="mb-4 bg-light p-3 rounded-4">
                        <div class="row text-center">
                            <div class="col">
                                <span class="d-block text-muted small uppercase fw-bold">Dequda Total</span>
                                <span class="fw-bold fs-5 text-dark" id="modal_total_amount"></span>
                            </div>
                            <div class="col border-start">
                                <span class="d-block text-muted small uppercase fw-bold">Deuda Pendiente</span>
                                <span class="fw-bold fs-5 text-danger" id="modal_pending_amount"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Monto del Abono</label>
                        <div class="input-group shadow-sm rounded-4 overflow-hidden">
                            <span class="input-group-text border-0 bg-white"><i class="fas fa-dollar-sign text-muted"></i></span>
                            <input type="number" step="0.01" id="payment_amount" class="form-control border-0 py-2" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Fecha de Pago</label>
                            <input type="date" id="payment_date" class="form-control border-0 shadow-sm rounded-4 py-2" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Método de Pago</label>
                            <select id="payment_method" class="form-select border-0 shadow-sm rounded-4 py-2" required>
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                                <option value="CHEQUE">Cheque</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Notas / Referencia</label>
                        <textarea id="payment_notes" class="form-control border-0 shadow-sm rounded-4 p-3" rows="2" placeholder="Nro de transferencia, observaciones..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-soft border-0" id="btnSavePayment">
                        <i class="fas fa-check-circle me-2"></i> CONFIRMAR PAGO
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
