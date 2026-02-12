@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Configuraciones Globales</h1>
            <p class="text-muted small mb-0">Ajustes generales para el centro de mando central.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white p-4 border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-envelope-open-text text-primary me-2"></i>Notificaciones de Pago</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('central.settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="admin_payment_emails" class="form-label fw-bold">Correos de Administración</label>
                            <textarea name="admin_payment_emails" id="admin_payment_emails" rows="3" class="form-control @error('admin_payment_emails') is-invalid @enderror" placeholder="admin1@ejemplo.com, admin2@ejemplo.com">{{ old('admin_payment_emails', $adminEmails) }}</textarea>
                            @error('admin_payment_emails')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                <i class="fas fa-info-circle me-1"></i> Ingrese uno o más correos electrónicos separados por comas. Estos correos recibirán los comprobantes que los inquilinos envíen desde su panel de bloqueo.
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3">
                                <i class="fas fa-save me-2"></i> Guardar Configuraciones
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4 overflow-hidden border-start border-warning">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3 text-warning">
                            <i class="fas fa-lightbulb fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold text-dark">¿Cómo funciona?</h6>
                            <p class="text-muted small mb-0">
                                Cuando un inquilino envía un comprobante, el sistema buscará primero si ese inquilino tiene un correo específico configurado en su perfil. Si no lo tiene, enviará la notificación a todos los correos que definas arriba.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
