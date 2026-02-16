@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Verifica tu email',
        'subtitle' => 'Te enviamos un enlace de verificación. Revisa tu correo para continuar.'
    ])

    <div class="d-grid gap-3 mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold w-100" type="submit">
                Reenviar Enlace
            </button>
        </form>
    
        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-muted">
                Cerrar sesión
            </button>
        </form>
    </div>
@endsection
