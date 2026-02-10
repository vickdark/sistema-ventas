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
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nro_compra" class="form-label">Número de Compra</label>
                                <input type="number" class="form-control rounded-3 bg-light @error('nro_compra') is-invalid @enderror" id="nro_compra" name="nro_compra" value="{{ old('nro_compra', $nextNroCompra) }}" required readonly>
                                @error('nro_compra')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="purchase_date" class="form-label">Fecha de Compra</label>
                                <input type="date" class="form-control rounded-3 @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label">Proveedor</label>
                                <select class="form-select rounded-3 @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                    <option value="" disabled selected>Selecciona un proveedor</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->company }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="voucher" class="form-label">Comprobante / Factura</label>
                                <input type="text" class="form-control rounded-3 @error('voucher') is-invalid @enderror" id="voucher" name="voucher" value="{{ old('voucher') }}" required placeholder="Ej: FAC-001">
                                @error('voucher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Producto</label>
                            <select class="form-select rounded-3 @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                <option value="" disabled selected>Selecciona un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-max-stock="{{ $product->max_stock }}" 
                                            data-current-stock="{{ $product->stock }}"
                                            {{ (old('product_id') ?? request('product_id')) == $product->id ? 'selected' : '' }}>
                                        {{ $product->code }} - {{ $product->name }} (Stock: {{ $product->stock }} / Max: {{ $product->max_stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Cantidad</label>
                                <input type="number" class="form-control rounded-3 @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" required min="1">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-4">
                                <label for="price" class="form-label">Precio Unitario de Compra</label>
                                <div class="input-group">
                                    <span class="input-group-text rounded-start-3">$</span>
                                    <input type="number" step="0.01" class="form-control rounded-end-3 @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required min="0">
                                </div>
                                @error('price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Confirmar y Registrar Compra
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let productSelectInstance;

        if (window.TomSelect) {
            new TomSelect('#supplier_id', {
                create: false,
                sortField: { field: 'text', direction: 'asc' }
            });

            productSelectInstance = new TomSelect('#product_id', {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                onChange: validateStock
            });
        }

        const quantityInput = document.getElementById('quantity');
        const quantityFeedback = document.querySelector('#quantity + .invalid-feedback') || createFeedbackElement(quantityInput);
        
        quantityInput.addEventListener('input', validateStock);

        function createFeedbackElement(input) {
            const div = document.createElement('div');
            div.className = 'invalid-feedback';
            input.parentNode.appendChild(div);
            return div;
        }

        function validateStock() {
            const productId = document.getElementById('product_id').value;
            const quantity = parseInt(quantityInput.value) || 0;
            
            if (!productId) return;

            // Find the option element in the original select to get data attributes
            // TomSelect hides it but keeps it updated partially, but we need to find the option by value
            const originalSelect = document.getElementById('product_id');
            const selectedOption = originalSelect.querySelector(`option[value="${productId}"]`);

            if (!selectedOption) return;

            const maxStock = parseInt(selectedOption.getAttribute('data-max-stock')) || 0;
            const currentStock = parseInt(selectedOption.getAttribute('data-current-stock')) || 0;

            // Si maxStock es 0, asumimos que no hay límite o no se configuró, pero el usuario pidió validar
            // que NO supere el stock máximo. Si max_stock está definido en BD, lo usamos.
            if (maxStock > 0) {
                if ((currentStock + quantity) > maxStock) {
                    quantityInput.classList.add('is-invalid');
                    quantityFeedback.textContent = `La cantidad supera el stock máximo permitido (${maxStock}). Stock actual: ${currentStock}. Espacio disponible: ${maxStock - currentStock}`;
                    quantityFeedback.style.display = 'block';
                } else {
                    quantityInput.classList.remove('is-invalid');
                    quantityFeedback.style.display = 'none';
                }
            }
        }
        
        // Ejecutar validación inicial si hay valores (ej: old input o redirect)
        setTimeout(validateStock, 500);
    });
</script>
@endsection
