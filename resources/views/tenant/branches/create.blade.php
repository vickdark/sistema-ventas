@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Nueva Sucursal</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('branches.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('branches.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6 text-start">
                                <label for="name" class="form-label">Nombre de la Sucursal</label>
                                <input type="text" 
                                       class="form-control rounded-3 @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ej: Sucursal Centro" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 text-start">
                                <label for="phone" class="form-label">Teléfono</label>
                                <input type="text" 
                                       class="form-control rounded-3 @error('phone') is-invalid @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="Ej: +51 987654321">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 text-start">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" 
                                   class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="contacto@sucursal.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 text-start">
                            <label for="address" class="form-label">Dirección Física</label>
                            <textarea class="form-control rounded-3 @error('address') is-invalid @enderror" 
                                      id="address" 
                                      name="address" 
                                      rows="2" 
                                      placeholder="Calle, Número, Distrito...">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 text-start">
                            <div class="form-check form-switch p-3 bg-light rounded-3">
                                <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_main" name="is_main" value="1">
                                <label class="form-check-label fw-bold" for="is_main">¿Establecer como Sucursal Principal?</label>
                                <p class="text-muted small mb-0 ms-4">La sucursal principal será asignada por defecto a los nuevos usuarios y se usará como referencia central.</p>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Sucursal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
