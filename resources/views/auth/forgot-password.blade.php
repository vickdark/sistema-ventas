@extends('layouts.guest')

@section('content')
    <div class="mb-4">
        <h1 class="h4 fw-semibold">Recuperar contrasena</h1>
        <p class="text-secondary mb-0">Te enviamos un enlace para restablecerla.</p>
    </div>

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

        <div class="d-flex justify-content-between align-items-center">
            <a class="small text-decoration-none" href="{{ route('login') }}">Volver al login</a>
            <button class="btn btn-brand text-white" type="submit">Enviar enlace</button>
        </div>
    </form>
@endsection
