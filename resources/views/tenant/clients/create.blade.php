@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="clients-create-page"></div>

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Clientes</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('clients.store') }}" method="POST" id="clientsForm">
                        @csrf
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Clientes a Registrar</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addClient">
                                <i class="fas fa-plus me-1"></i> Agregar Otro (Máx. 5)
                            </button>
                        </div>

                        <div id="clientsContainer">
                            <!-- Primer cliente por defecto -->
                            <div class="client-item mb-4 p-4 border rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">Cliente #1</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-client" style="width: 38px; height: 38px;" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control rounded-3" name="clients[0][name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIT/CI</label>
                                        <input type="text" class="form-control rounded-3" name="clients[0][nit_ci]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" class="form-control rounded-3" name="clients[0][phone]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control rounded-3" name="clients[0][email]" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-1"></i> 
                            Puedes registrar hasta 5 clientes a la vez. Cada uno debe tener un NIT/CI único.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Clientes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
