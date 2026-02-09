@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold">Iniciar sesion</h1>
        <p class="text-secondary mb-0">Accede a tu cuenta.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Contrasena</label>
            <input
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                type="password"
                name="password"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">Recordarme</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <a class="small text-decoration-none" href="{{ route('password.request') }}">
                Olvidaste tu contrasena?
            </a>
            <button class="btn btn-primary text-white" type="submit">Entrar</button>
        </div>
    </form>
@endsection
