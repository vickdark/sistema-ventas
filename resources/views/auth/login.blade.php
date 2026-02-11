@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Bienvenido de nuevo',
        'subtitle' => 'Ingresa tus credenciales para acceder al sistema.'
    ])

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

        <div class="d-grid gap-3 mt-4">
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold" type="submit">
                Entrar al Sistema
            </button>
            <div class="text-center">
                <a class="small text-decoration-none text-muted" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </div>
    </form>
@endsection
