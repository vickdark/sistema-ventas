@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Restablecer contraseña',
        'subtitle' => 'Define una nueva contraseña para tu cuenta.'
    ])

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email) }}"
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

        <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirmar contrasena</label>
            <input
                class="form-control"
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
            >
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold" type="submit">
                Restablecer Contraseña
            </button>
        </div>
    </form>
@endsection
