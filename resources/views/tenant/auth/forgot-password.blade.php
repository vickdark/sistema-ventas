@extends('layouts.guest')

@section('content')
    @include('partials.auth-header', [
        'title' => 'Recuperar contraseña',
        'subtitle' => 'Te enviaremos un enlace para restablecerla.'
    ])

    @if (session('restricted_role'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: 'Acceso Restringido',
                        text: "{{ session('restricted_role') }}",
                        confirmButtonColor: '#4e73df',
                        confirmButtonText: 'Entendido'
                    });
                }
            });
        </script>
    @endif

    <div id="loadingAlert" class="alert alert-info d-none mb-4 shadow-sm border-0 rounded-4 p-3">
        <div class="d-flex align-items-center">
            <div class="spinner-border spinner-border-sm text-info me-3" role="status"></div>
            <div>
                <h6 class="mb-0 fw-bold">Enviando enlace de recuperación...</h6>
                <small class="text-muted">Por favor, espera un momento mientras procesamos tu solicitud.</small>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('password.email') }}" id="forgotPasswordForm">
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

        <div class="d-grid gap-3 mt-4" id="formActions">
            <button class="btn btn-primary btn-lg text-white rounded-pill shadow-sm py-2 fw-bold" type="submit" id="submitBtn">
                Enviar Enlace de Recuperación
            </button>
            <div class="text-center">
                <a class="small text-decoration-none text-muted" href="{{ route('login') }}">
                    Volver al login
                </a>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
            const formActions = document.getElementById('formActions');
            const loadingAlert = document.getElementById('loadingAlert');
            
            // Ocultar botones y mostrar alerta de carga
            formActions.classList.add('d-none');
            loadingAlert.classList.remove('d-none');
        });
    </script>
@endsection
