@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Abono</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('abonos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver a Cartera
            </a>
        </div>
    </div>

    <!-- KPI Cards (Hidden until client selected) -->
    <div id="debtSummaryCards" class="row mb-4 d-none">
        <div class="col-md-4">
            <div class="card border-0 shadow-soft rounded-4 border-start border-primary border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Deuda Total Acumulada</h6>
                    <h3 class="fw-bold mb-0 text-primary" id="totalInvoicedCard">$0.00</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-soft rounded-4 border-start border-success border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Total Abonado</h6>
                    <h3 class="fw-bold mb-0 text-success" id="totalAbonosCard">$0.00</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-soft rounded-4 border-start border-danger border-4">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase">Saldo Pendiente Real</h6>
                    <h3 class="fw-bold mb-0 text-danger" id="totalDebtCard">$0.00</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Panel Izquierdo: Formulario -->
        <div class="col-md-5">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Datos del Abono</h5>
                    <form id="abonoForm">
                        <div class="mb-3">
                            <label for="client_id" class="form-label fw-bold">Cliente</label>
                            <select id="client_id" name="client_id" class="form-select rounded-3 border-light bg-light shadow-sm" required>
                                <option value="">Seleccione un cliente...</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->nit_ci }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="pendingSalesContainer" class="mb-3 d-none">
                            <label for="sale_id" class="form-label fw-bold small">Venta Específica (Opcional)</label>
                            <select id="sale_id" name="sale_id" class="form-select form-select-sm rounded-3 border-light bg-light shadow-sm">
                                <option value="">Abono General (Distribuir en deudas)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="amount" class="form-label fw-bold">Monto a abonar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-light text-muted">$</span>
                                <input type="number" step="0.01" class="form-control form-control-lg border-light bg-light fw-bold text-primary" id="amount" name="amount" required min="0.01">
                            </div>
                        </div>

                        <button type="submit" id="btnSubmit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">
                            <i class="fas fa-save me-2"></i> Registrar Abono Ahora
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Historial Recent del Cliente -->
        <div class="col-md-7">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 border-bottom pb-2">Historial de Abonos del Cliente</h5>
                    <div id="noClientSelected" class="text-center py-5 text-muted opacity-50">
                        <i class="fas fa-file-invoice-dollar fa-4x mb-3"></i>
                        <p>Seleccione un cliente para ver su historial</p>
                    </div>
                    <div id="historyTableContainer" class="table-responsive d-none">
                        <table class="table table-hover align-middle small">
                            <thead class="bg-light">
                                <tr>
                                    <th>FECHA</th>
                                    <th>REFERENCIA</th>
                                    <th class="text-end">MONTO</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody">
                                <!-- Ajax content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initAbonosCreate({
            routes: {
                store: "{{ route('abonos.store') }}",
                index: "{{ route('abonos.index') }}",
                pendingSales: "{{ route('abonos.pending-sales', ':id') }}",
                summary: "{{ route('abonos.debt-summary', ':id') }}",
                history: "{{ route('abonos.client-history', ':id') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
