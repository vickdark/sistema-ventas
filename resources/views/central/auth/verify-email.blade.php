@extends('layouts.guest')

@section('content')
    <div class="mb-3 text-muted">
        {{ __('Gracias por registrarte! Antes de comenzar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que te acabamos de enviar? Si no recibiste el correo, con gusto te enviaremos otro.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success mb-3">
            {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste durante el registro.') }}
        </div>
    @endif

    <div class="mt-4 d-flex align-items-center justify-content-between">
        <form method="POST" action="{{ route('central.verification.send') }}">
            @csrf

            <div>
                <button type="submit" class="btn btn-primary">
                    {{ __('Reenviar correo de verificación') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('central.logout') }}">
            @csrf

            <button type="submit" class="btn btn-danger">
                {{ __('Cerrar sesión') }}
            </button>
        </form>
    </div>
@endsection