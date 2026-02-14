@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">{{ __('Gestión de Usuarios Centrales') }}</h1>
            <p class="text-muted small mb-0">{{ __('Administra los usuarios que tienen acceso al panel central del sistema.') }}</p>
        </div>
        <div class="col-auto">
            @auth('owner')
                @if (!Auth::guard('owner')->user()->hasVerifiedEmail())
                    <form method="POST" action="{{ route('central.verification.send') }}" class="d-inline me-2">
                        @csrf
                        <button type="submit" class="btn btn-warning rounded-pill px-4 shadow-sm transition-all hover-lift">
                            <i class="fas fa-envelope me-2"></i>{{ __('Reenviar Correo de Verificación') }}
                        </button>
                    </form>
                @endif
            @endauth
            <a href="{{ route('central.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm transition-all hover-lift">
                <i class="fas fa-plus-circle me-2"></i>{{ __('Crear Nuevo Usuario') }}
            </a>
        </div>
    </div>
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">

                    <div class="card-body p-4">
                        <div id="users-grid"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.initCentralUsersIndex) {
                window.initCentralUsersIndex('users-grid', {
                    routes: {
                        index: "{{ route('central.users.index') }}",
                        create: "{{ route('central.users.create') }}",
                        edit: "{{ route('central.users.edit', ['user' => ':id']) }}",
                        destroy: "{{ route('central.users.destroy', ['user' => ':id']) }}",
                        resendVerification: "{{ route('central.users.resend-verification') }}"
                    },
                    tokens: {
                        csrf: "{{ csrf_token() }}"
                    }
                });
            }
        });
    </script>
@endsection

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
    }
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
    .x-small {
        font-size: 0.75rem;
    }
    /* Alinear encabezados de Grid.js */
    .gridjs-th:nth-last-child(1), 
    .gridjs-th:nth-last-child(2) {
        text-align: center !important;
    }
    .gridjs-td:nth-last-child(1),
    .gridjs-td:nth-last-child(2) {
        text-align: center !important;
    }
</style>