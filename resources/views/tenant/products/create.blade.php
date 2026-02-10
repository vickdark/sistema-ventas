@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Crear Producto</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Categoría</label>
                            <select class="form-select rounded-3 @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="" selected disabled>Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Código</label>
                            <input type="text" class="form-control rounded-3 @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control rounded-3 @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="min_stock" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control rounded-3 @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock') }}" required>
                            @error('min_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_stock" class="form-label">Stock Máximo</label>
                            <input type="number" class="form-control rounded-3 @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock') }}" required>
                            @error('max_stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_price" class="form-label">Precio de Compra</label>
                            <input type="number" step="0.01" class="form-control rounded-3 @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required>
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sale_price" class="form-label">Precio de Venta</label>
                            <input type="number" step="0.01" class="form-control rounded-3 @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" required>
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="entry_date" class="form-label">Fecha de Entrada</label>
                            <input type="date" class="form-control rounded-3 @error('entry_date') is-invalid @enderror" id="entry_date" name="entry_date" value="{{ old('entry_date') }}" required>
                            @error('entry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">URL de Imagen</label>
                            <input type="text" class="form-control rounded-3 @error('image') is-invalid @enderror" id="image" name="image" value="{{ old('image') }}">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
