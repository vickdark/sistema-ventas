@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Abrir Caja</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('cash-registers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form action="{{ route('cash-registers.store') }}" method="POST">
                        @csrf
                        <div class="mb-4 text-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-4 d-inline-block mb-3">
                                <i class="fas fa-unlock fa-3x text-primary"></i>
                            </div>
                            <p class="text-muted">Inicia una nueva sesión de caja para comenzar a vender.</p>
                        </div>

                        <div class="mb-3">
                            <label for="opening_date" class="form-label">Fecha y Hora de Apertura</label>
                            <input type="datetime-local" class="form-control rounded-3 @error('opening_date') is-invalid @enderror" id="opening_date" name="opening_date" value="{{ old('opening_date', date('Y-m-d\TH:i')) }}" required>
                            @error('opening_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="initial_amount" class="form-label">Monto Inicial (Base de Caja)</label>
                            <div class="input-group">
                                <span class="input-group-text rounded-start-3">$</span>
                                <input type="number" step="0.01" class="form-control rounded-end-3 @error('initial_amount') is-invalid @enderror" id="initial_amount" name="initial_amount" value="{{ old('initial_amount', '0.00') }}" required min="0">
                            </div>
                            @error('initial_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="observations" class="form-label">Observaciones de Apertura (Opcional)</label>
                            <textarea class="form-control rounded-3 @error('observations') is-invalid @enderror" id="observations" name="observations" rows="2">{{ old('observations') }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($config && $config->cash_register_closing_time)
                            <div class="alert alert-info rounded-3 small mb-4">
                                <i class="fas fa-clock me-2"></i> 
                                El cierre automático está programado para las <strong>{{ $config->cash_register_closing_time }}</strong>.
                            </div>
                        @endif

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-check me-2"></i> Confirmar Apertura
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
