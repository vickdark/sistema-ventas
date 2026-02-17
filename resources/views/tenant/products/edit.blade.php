@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="products-edit-page"></div>

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Editar Producto</h1>
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
                    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="code" class="form-label">Código</label>
                                <input type="text" class="form-control rounded-3 @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $product->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control rounded-3 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label">Categoría</label>
                                <select class="form-select rounded-3 @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="" disabled>Selecciona una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purchase_price" class="form-label">Precio de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3">$</span>
                                    <input type="number" step="0.01" class="form-control rounded-end-3 @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" required>
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Precio de Venta</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3">$</span>
                                    <input type="number" step="0.01" class="form-control rounded-end-3 @error('sale_price') is-invalid @enderror" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" required>
                                </div>
                                @error('sale_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock" class="form-label">Stock Actual</label>
                                <input type="number" class="form-control rounded-3 @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="min_stock" class="form-label">Stock Mínimo</label>
                                <input type="number" class="form-control rounded-3 @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" required>
                                @error('min_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="max_stock" class="form-label">Stock Máximo</label>
                                <input type="number" class="form-control rounded-3 @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock', $product->max_stock) }}" required>
                                @error('max_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="entry_date" class="form-label">Fecha de Entrada</label>
                                <input type="date" class="form-control rounded-3 @error('entry_date') is-invalid @enderror" id="entry_date" name="entry_date" value="{{ old('entry_date', $product->entry_date) }}" required>
                                @error('entry_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="image_file" class="form-label">Imagen del Producto (Opcional)</label>
                                @if($product->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="Preview" class="img-thumbnail" style="height: 60px;">
                                        <small class="text-muted d-block">Imagen actual</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control rounded-3 @error('image_file') is-invalid @enderror" id="image_file" name="image_file" accept="image/*">
                                <div class="form-text small opacity-75">
                                    El archivo debe ser una imagen.<br>
                                    Formatos permitidos: jpeg, png, jpg, gif.
                                </div>
                                @error('image_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="supplier_ids" class="form-label fw-bold">Proveedores (Requerido)</label>
                            <select class="form-select @error('supplier_ids') is-invalid @enderror" id="supplier_ids" name="supplier_ids[]" multiple placeholder="Busca y selecciona proveedores..." required>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" 
                                        {{ in_array($supplier->id, old('supplier_ids', $product->suppliers->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $supplier->name }} {{ $supplier->company ? '('.$supplier->company.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_ids')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text small opacity-75 mt-1">
                                <i class="fas fa-info-circle me-1"></i> Selecciona uno o varios proveedores para este producto.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Descripción (Opcional)</label>
                            <textarea class="form-control rounded-3 @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-sync me-2"></i> Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
