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
            <div class="card settings-card mb-4">
                <div class="card-header settings-header">
                    <h5 class="settings-title"><i class="fas fa-envelope-open-text text-primary settings-icon"></i>Notificaciones de Pago</h5>
                </div>
                <div class="card-body settings-body">
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

            <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white p-4 border-bottom">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-key text-primary me-2"></i>Gestión de Clave de Acceso</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('central.gate_key.update') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="gate_key" class="form-label fw-semibold">Clave de Acceso Actual</label>
                            <input type="text" class="form-control @error('gate_key') is-invalid @enderror" id="gate_key" name="gate_key" value="{{ old('gate_key', $currentKey) }}" required minlength="4">
                            @error('gate_key')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Esta clave protege el acceso al login central. Mínimo 4 caracteres.</small>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 fw-bold rounded-3">
                                <i class="fas fa-save me-2"></i>Actualizar Clave
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card settings-card settings-info-box">
                <div class="card-body settings-body">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3 text-warning">
                            <i class="fas fa-lightbulb fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold text-dark">¿Cómo funciona?</h6>
                            <p class="text-muted small mb-0">
                                Cuando un inquilino envía un comprobante, el sistema enviará la notificación a todos los correos que definas arriba.
                            </p>
                            <p class="text-muted small mb-0">
                                la clave sirve como doble validacion para que solo los administradores puedan acceder al login central.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
