@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gestión de Clave de Acceso</h1>
            <p class="text-muted small mb-0">Administra la clave de acceso para el login central.</p>
        </div>
    </div>

    <div class="card settings-card mb-4">
        <div class="card-body settings-body">
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('central.gate_key.update') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="gate_key" class="form-label fw-semibold">Clave de Acceso Actual</label>
                    <input type="text" class="form-control @error('gate_key') is-invalid @enderror" id="gate_key" name="gate_key" value="{{ old('gate_key', $currentKey) }}" required minlength="4">
                    @error('gate_key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Esta clave protege el acceso al login central. Mínimo 4 caracteres.</small>
                </div>
                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="fas fa-save me-2"></i>Actualizar Clave
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
