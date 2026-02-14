@extends('layouts.guest')

@section('content')
@if (!Session::has('central_gate_passed'))
<div id="gate-container">
    <div class="text-center mb-5">
        <div class="app-brand-logo bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 64px; height: 64px;">
            <i class="fa-solid fa-key text-primary fs-2"></i>
        </div>
        <h2 class="fw-bold text-gray-900 mb-1">Acceso Restringido</h2>
        <p class="text-muted small">Por favor, introduce la clave de acceso para continuar.</p>
    </div>

    <form method="POST" action="{{ route('central.gate.verify') }}">
        @csrf
        <div class="mb-4">
            <label for="gate_key" class="form-label fw-semibold small text-uppercase text-muted">Clave de Acceso</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                <input id="gate_key" type="password" class="form-control bg-light border-start-0 @error('gate_key') is-invalid @enderror" 
                    name="gate_key" required placeholder="Introduce la clave">
            </div>
            @error('gate_key')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-sm fw-bold transition-all hover-lift">
            Verificar Clave
        </button>
    </form>
</div>
@endif

<div id="login-form-container" @if (!Session::has('central_gate_passed')) style="display: none;" @endif>
    <div class="text-center mb-5">
        <div class="app-brand-logo bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm mb-3" style="width: 64px; height: 64px;">
            <i class="fa-solid fa-shield-halved text-primary fs-2"></i>
        </div>
        <h2 class="fw-bold text-gray-900 mb-1">Central Admin</h2>
        <p class="text-muted small">Acceso exclusivo para el administrador del sistema</p>
    </div>

    <form method="POST" action="{{ route('central.login.submit') }}" class="needs-validation" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label fw-semibold small text-uppercase text-muted">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-regular fa-envelope text-muted"></i></span>
                <input id="email" type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                    name="email" value="{{ old('email') }}" required autofocus placeholder="admin@example.com">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label fw-semibold small text-uppercase text-muted">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-lock text-muted"></i></span>
                <input id="password" type="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" 
                    name="password" required placeholder="••••••••">
            </div>
            @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember">
            <label class="form-check-label small text-muted" for="remember">Recordar sesión</label>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-sm fw-bold transition-all hover-lift">
            Iniciar Sesión Central
        </button>
    </form>
</div>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
    }
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection
