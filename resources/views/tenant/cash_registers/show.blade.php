@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Detalles de Sesión de Caja #{{ $cashRegister->id }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 text-center">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center">
                    <div class="rounded-circle {{ $cashRegister->status === 'abierta' ? 'bg-success' : 'bg-secondary' }} bg-opacity-10 p-4 mb-3">
                        <i class="fas {{ $cashRegister->status === 'abierta' ? 'fa-cash-register' : 'fa-archive' }} fa-3x {{ $cashRegister->status === 'abierta' ? 'text-success' : 'text-secondary' }}"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Estado de Sesión</h5>
                    <span class="badge rounded-pill {{ $cashRegister->status === 'abierta' ? 'bg-success' : 'bg-secondary' }} mb-3 py-2 px-3 fs-6">
                        {{ strtoupper($cashRegister->status) }}
                    </span>
                    <p class="text-muted small mb-0">Usuario: <strong>{{ $cashRegister->user->name }}</strong></p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Apertura</h6>
                            <div class="fs-5 fw-bold text-dark">{{ \Carbon\Carbon::parse($cashRegister->opening_date)->format('d/m/Y H:i') }}</div>
                            <div class="text-muted small">Monto Inicial: <span class="fw-bold text-primary">${{ number_format($cashRegister->initial_amount, 2) }}</span></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-uppercase text-muted fw-bold small mb-2">Cierre</h6>
                            @if($cashRegister->closing_date)
                                <div class="fs-5 fw-bold text-dark">{{ \Carbon\Carbon::parse($cashRegister->closing_date)->format('d/m/Y H:i') }}</div>
                                <div class="text-muted small">Monto Final: <span class="fw-bold text-danger">${{ number_format($cashRegister->final_amount, 2) }}</span></div>
                            @else
                                <div class="text-muted italic">Pendiente de cierre</div>
                                @if($cashRegister->scheduled_closing_time)
                                    <div class="text-muted small">Cierre programado: <span class="fw-bold">{{ $cashRegister->scheduled_closing_time }}</span></div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="mb-0">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Observaciones</h6>
                        <div class="p-3 bg-light rounded-3 text-secondary">
                            {{ $cashRegister->observations ?: 'Sin observaciones registradas.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Totals Summary -->
    <div class="row g-4 py-4 mb-2">
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 border-start border-primary border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Dinero Inicial</h6>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                            <i class="fas fa-wallet text-primary"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0">${{ number_format($cashRegister->initial_amount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 border-start border-success border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Ventas Turno</h6>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i class="fas fa-shopping-cart text-success"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">${{ number_format($totalSalesValue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 border-start border-info border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-muted small text-uppercase fw-bold mb-0">Abonos Turno</h6>
                        <div class="rounded-circle bg-info bg-opacity-10 p-2">
                            <i class="fas fa-hand-holding-dollar text-info"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-info">${{ number_format($totalAbonos, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-soft rounded-4 border-start border-primary border-4 bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="text-primary small text-uppercase fw-bold mb-0">Ingresos Totales</h6>
                        <div class="rounded-circle bg-primary bg-opacity-25 p-2">
                            <i class="fas fa-plus-circle text-primary"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-primary">${{ number_format($totalIncome, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Summary Section -->
    <div class="row pt-2 align-items-stretch">
        <!-- Sales Summary -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart me-2"></i> Ventas Directas del Turno</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Folio</th>
                                    <th>Cliente</th>
                                    <th>Hora</th>
                                    <th class="text-end pe-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($directSales as $sale)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $sale->nro_venta }}</td>
                                    <td>{{ optional($sale->client)->name ?? 'Consumidor Final' }}</td>
                                    <td class="text-muted">{{ $sale->created_at->format('H:i') }}</td>
                                    <td class="text-end pe-4 fw-bold text-success">${{ number_format($sale->total_paid, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay ventas directas en este turno.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end py-2">Total Ventas Directas:</td>
                                    <td class="text-end pe-4 py-2">${{ number_format($totalSalesValue, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Abonos Summary -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-hand-holding-dollar me-2"></i> Abonos Recibidos (Cartera)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="bg-light sticky-top">
                                <tr>
                                    <th class="ps-4">Cliente</th>
                                    <th>Concepto</th>
                                    <th>Hora</th>
                                    <th class="text-end pe-4">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($abonos as $abono)
                                <tr>
                                    <td class="ps-4 fw-bold text-truncate" style="max-width: 150px;">{{ optional($abono->client)->name }}</td>
                                    <td>
                                        @if($abono->sale_id)
                                            Venta #{{ optional($abono->sale)->nro_venta }}
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">General</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $abono->created_at->format('H:i') }}</td>
                                    <td class="text-end pe-4 fw-bold text-success">${{ number_format($abono->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No hay abonos registrados en este turno.</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end py-2">Total Abonos:</td>
                                    <td class="text-end pe-4 py-2">${{ number_format($totalAbonos, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
