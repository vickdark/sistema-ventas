@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Recuperar contraseña',
        'subtitle' => 'Te enviaremos un enlace para restablecerla.'
    ])

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
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

        <div class="d-grid gap-3 mt-4">
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold" type="submit">
                Enviar Enlace de Recuperación
            </button>
            <div class="text-center">
                <a class="small text-decoration-none text-muted" href="{{ route('login') }}">
                    Volver al login
                </a>
            </div>
        </div>
    </form>
@endsection
