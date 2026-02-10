@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cerrar Caja</h1>
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
                                    <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">Ventas (Contado)</small>
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
</div>
@endsection
