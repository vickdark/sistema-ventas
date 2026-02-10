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

                    @if($cashRegister->status === 'cerrada')
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Resumen de Ventas</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Ventas Realizadas</label>
                                <span class="fw-bold fs-5">{{ $cashRegister->sales_count ?? 0 }} ventas</span>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted d-block small">Total Recaudado</label>
                                <span class="fw-bold fs-5 text-success">${{ number_format($cashRegister->total_sales ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

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
</div>
@endsection
