@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Compra</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('purchases.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto</label>
                            <select class="form-select rounded-3 @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="" disabled selected>Selecciona un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="supplier_id" class="form-label">Proveedor</label>
                            <select class="form-select rounded-3 @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="" disabled selected>Selecciona un proveedor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Cantidad</label>
                            <input type="number" class="form-control rounded-3 @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control rounded-3 @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control rounded-3 @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                            @error('purchase_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="voucher" class="form-label">Comprobante</label>
                            <input type="text" class="form-control rounded-3 @error('voucher') is-invalid @enderror" id="voucher" name="voucher" value="{{ old('voucher') }}">
                            @error('voucher')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Registrar Compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
