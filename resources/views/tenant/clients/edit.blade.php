@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Editar Cliente</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('clients.update', $client) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $client->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nit_ci" class="form-label">NIT/CI</label>
                            <input type="text" class="form-control rounded-3 @error('nit_ci') is-invalid @enderror" id="nit_ci" name="nit_ci" value="{{ old('nit_ci', $client->nit_ci) }}">
                            @error('nit_ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control rounded-3 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $client->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $client->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="address" class="form-label">Dirección</label>
                            <input type="text" class="form-control rounded-3 @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $client->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-sync me-2"></i> Actualizar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
