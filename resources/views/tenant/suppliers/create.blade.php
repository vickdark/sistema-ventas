@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Proveedores</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('suppliers.store') }}" method="POST" id="suppliersForm">
                        @csrf
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Proveedores a Registrar</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addSupplier">
                                <i class="fas fa-plus me-1"></i> Agregar Otro (Máx. 5)
                            </button>
                        </div>

                        <div id="suppliersContainer">
                            <!-- Primer proveedor por defecto -->
                            <div class="supplier-item mb-4 p-4 border rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">Proveedor #1</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-supplier supplier-remove-btn" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre del Contacto</label>
                                        <input type="text" class="form-control rounded-3" name="suppliers[0][name]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nombre de la Empresa</label>
                                        <input type="text" class="form-control rounded-3" name="suppliers[0][company]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono Principal</label>
                                        <input type="text" class="form-control rounded-3" name="suppliers[0][phone]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Teléfono Secundario</label>
                                        <input type="text" class="form-control rounded-3" name="suppliers[0][secondary_phone]">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control rounded-3" name="suppliers[0][email]">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <textarea class="form-control rounded-3" name="suppliers[0][address]" rows="2" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-1"></i> 
                            Puedes registrar hasta 5 proveedores a la vez. Cada uno debe tener un teléfono principal único.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Proveedores
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
