@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cerrar {{ $cashRegister->name }}</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form action="{{ route('cash-registers.close', $cashRegister) }}" method="POST">
                        @csrf
                        <div class="mb-4 text-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-4 d-inline-block mb-3">
                                <i class="fas fa-lock fa-3x text-danger"></i>
                            </div>
                            <p class="text-muted">Finaliza la sesión de caja actual y registra los totales.</p>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-3 text-center">
                                <div class="p-2 bg-light rounded-3">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Monto Inicial</small>
                                    <span class="fs-5 fw-bold text-dark">${{ number_format($cashRegister->initial_amount, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-2 bg-light rounded-3">
                                    <small class="text-muted d-block text-uppercase fw-bold cash-register-label-sm">Ventas (Contado)</small>
                                    <span class="fs-5 fw-bold text-success">${{ number_format($totalSalesValue, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-2 bg-light rounded-3">
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Abonos (Cartera)</small>
                                    <span class="fs-5 fw-bold text-success">${{ number_format($totalAbonos, 2) }}</span>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="p-2 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25">
                                    <small class="text-primary d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Total Esperado</small>
                                    <span class="fs-5 fw-bold text-primary">${{ number_format($expectedAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="final_amount" class="form-label">Monto Final Real (Conteo Físico)</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-3">$</span>
                                <input type="number" step="0.01" class="form-control rounded-end-3 @error('final_amount') is-invalid @enderror" id="final_amount" name="final_amount" value="{{ old('final_amount', $expectedAmount) }}" required min="0">
                            </div>
                            @error('final_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted mt-1 d-block">Ingresa el monto total de dinero físico que hay en la caja en este momento.</small>
                        </div>

                        <div class="mb-4">
                            <label for="observations" class="form-label">Observaciones de Cierre (Diferencias, novedades, etc.)</label>
                            <textarea class="form-control rounded-3 @error('observations') is-invalid @enderror" id="observations" name="observations" rows="3">{{ old('observations') }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid shadow-sm">
                            <button type="submit" class="btn btn-danger rounded-pill py-2 fw-bold">
                                <i class="fas fa-lock me-2"></i> Cerrar Caja Definitivamente
                            </button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>

    <!-- Detailed Summary Section -->
    <div class="row mt-4">
        <!-- Sales Summary -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-shopping-cart me-2"></i> Ventas Directas del Turno</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
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
                                    <td colspan="3" class="text-end py-2">Total Ventas:</td>
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
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
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
