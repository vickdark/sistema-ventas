@extends('layouts.guest')

@section('content')
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

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
    }
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection
