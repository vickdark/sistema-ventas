@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('expense-categories.index') }}" class="btn btn-icon btn-light rounded-circle shadow-sm me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Editar Categoría</h1>
                    <p class="text-muted mb-0">Modifica los detalles de la clasificación: <strong>{{ $expenseCategory->name }}</strong></p>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-5">
                    <form action="{{ route('expense-categories.update', $expenseCategory) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name', $expenseCategory->name) }}" placeholder="Nombre de la categoría" required>
                                    <label for="name">Nombre de la Categoría</label>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="color" class="form-label fw-bold text-muted small text-uppercase">Color Identificador</label>
                                <div class="d-flex align-items-center p-3 border rounded-3 bg-light">
                                    <input type="color" class="form-control form-control-color border-0 me-3" id="color" name="color" value="{{ old('color', $expenseCategory->color) }}" title="Choose your color">
                                    <span class="text-muted small">Selecciona un color para identificar rápidamente esta categoría en los reportes.</span>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-floating">
                                    <textarea class="form-control rounded-3" placeholder="Descripción opcional" id="description" name="description" style="height: 100px">{{ old('description', $expenseCategory->description) }}</textarea>
                                    <label for="description">Descripción (Opcional)</label>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $expenseCategory->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">Categoría Activa</label>
                                </div>
                                <div class="form-text">Las categorías inactivas no aparecerán al registrar nuevos gastos.</div>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                                <i class="fas fa-save me-2"></i> Actualizar Categoría
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
