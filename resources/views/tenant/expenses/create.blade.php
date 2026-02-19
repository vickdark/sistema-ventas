@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Nuevo Gasto</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Gastos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo</li>
                </ol>
            </nav>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('expenses.store') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Información General</h5>
                                <div class="mb-3">
                                    <label for="expense_category_id" class="form-label fw-bold">Categoría</label>
                                    <select class="form-select rounded-3 p-3 @error('expense_category_id') is-invalid @enderror" id="expense_category_id" name="expense_category_id" required>
                                        <option value="" selected disabled>Seleccione una categoría...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('expense_category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Clasificar correctamente ayuda en los reportes.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label fw-bold">Concepto</label>
                                    <input type="text" class="form-control rounded-3 p-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ej: Pago de luz, Artículos oficina..." required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="reference" class="form-label fw-bold">Referencia (Opcional)</label>
                                    <input type="text" class="form-control rounded-3 p-3" id="reference" name="reference" value="{{ old('reference') }}" placeholder="Nro Factura / Recibo / Transacción">
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6 border-start-md ps-md-4">
                                <h5 class="mb-3 text-success"><i class="fas fa-dollar-sign me-2"></i>Detalles del Pago</h5>
                                
                                <div class="mb-3">
                                    <label for="date" class="form-label fw-bold">Fecha del Gasto</label>
                                    <input type="date" class="form-control rounded-3 p-3 @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label fw-bold">Monto Total</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0 py-3"><i class="fas fa-dollar-sign"></i></span>
                                        <input type="number" step="0.01" class="form-control rounded-end-3 py-3 border-start-0 ps-0 @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required>
                                    </div>
                                    @error('amount')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                 <div class="mb-3">
                                    <label for="description" class="form-label fw-bold">Notas Adicionales</label>
                                    <textarea class="form-control rounded-3" id="description" name="description" rows="4" placeholder="Detalles extra sobre este gasto...">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('expenses.index') }}" class="btn btn-light rounded-pill px-4 py-2 border">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> Registrar Gasto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
