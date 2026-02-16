@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Confirmar contraseña',
        'subtitle' => 'Confirma tu contraseña para continuar.'
    ])

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="password">Contrasena</label>
            <input
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                type="password"
                name="password"
                required
                autofocus
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid mt-4">
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold" type="submit">
                Confirmar Contraseña
            </button>
        </div>
    </form>
@endsection
