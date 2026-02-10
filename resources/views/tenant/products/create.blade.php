@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Productos</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('products.store') }}" method="POST" id="productsForm">
                        @csrf
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Productos a Registrar</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addProduct">
                                <i class="fas fa-plus me-1"></i> Agregar Otro (Máx. 5)
                            </button>
                        </div>

                        <div id="productsContainer">
                            <!-- Primer producto por defecto -->
                            <div class="product-item mb-4 p-4 border rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 text-primary">Producto #1</h6>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-product" style="width: 38px; height: 38px;" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Código</label>
                                        <input type="text" class="form-control rounded-3" name="products[0][code]" required>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control rounded-3" name="products[0][name]" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Categoría</label>
                                        <select class="form-select rounded-3" name="products[0][category_id]" required>
                                            <option value="" selected disabled>Selecciona una categoría</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Precio Compra</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-3">$</span>
                                            <input type="number" step="0.01" class="form-control rounded-end-3" name="products[0][purchase_price]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Precio Venta</label>
                                        <div class="input-group">
                                            <span class="input-group-text rounded-start-3">$</span>
                                            <input type="number" step="0.01" class="form-control rounded-end-3" name="products[0][sale_price]" required>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Stock Actual</label>
                                        <input type="number" class="form-control rounded-3" name="products[0][stock]" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Stock Mín.</label>
                                        <input type="number" class="form-control rounded-3" name="products[0][min_stock]" required>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Stock Máx.</label>
                                        <input type="number" class="form-control rounded-3" name="products[0][max_stock]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Fecha de Entrada</label>
                                        <input type="date" class="form-control rounded-3" name="products[0][entry_date]" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL Imagen (Opcional)</label>
                                        <input type="text" class="form-control rounded-3" name="products[0][image]">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label">Descripción (Opcional)</label>
                                        <textarea class="form-control rounded-3" name="products[0][description]" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-1"></i> 
                            Puedes registrar hasta 5 productos a la vez. Cada uno debe tener un código único.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Productos
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
    const container = document.getElementById('productsContainer');
    const addBtn = document.getElementById('addProduct');
    let productCount = 1;
    const MAX_PRODUCTS = 5;

    // Template de categorías
    const categoryOptions = `
        <option value="" selected disabled>Selecciona una categoría</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
    `;

    addBtn.addEventListener('click', function() {
        if (productCount >= MAX_PRODUCTS) {
            alert('Solo puedes agregar hasta 5 productos a la vez.');
            return;
        }

        const newProduct = document.createElement('div');
        newProduct.className = 'product-item mb-4 p-4 border rounded-3 bg-light';
        newProduct.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">Producto #${productCount + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-product" style="width: 38px; height: 38px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control rounded-3" name="products[${productCount}][code]" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control rounded-3" name="products[${productCount}][name]" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select rounded-3" name="products[${productCount}][category_id]" required>
                        ${categoryOptions}
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Precio Compra</label>
                    <div class="input-group">
                        <span class="input-group-text rounded-start-3">$</span>
                        <input type="number" step="0.01" class="form-control rounded-end-3" name="products[${productCount}][purchase_price]" required>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Precio Venta</label>
                    <div class="input-group">
                        <span class="input-group-text rounded-start-3">$</span>
                        <input type="number" step="0.01" class="form-control rounded-end-3" name="products[${productCount}][sale_price]" required>
                    </div>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Stock Actual</label>
                    <input type="number" class="form-control rounded-3" name="products[${productCount}][stock]" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Stock Mín.</label>
                    <input type="number" class="form-control rounded-3" name="products[${productCount}][min_stock]" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">Stock Máx.</label>
                    <input type="number" class="form-control rounded-3" name="products[${productCount}][max_stock]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Fecha de Entrada</label>
                    <input type="date" class="form-control rounded-3" name="products[${productCount}][entry_date]" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">URL Imagen (Opcional)</label>
                    <input type="text" class="form-control rounded-3" name="products[${productCount}][image]">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Descripción (Opcional)</label>
                    <textarea class="form-control rounded-3" name="products[${productCount}][description]" rows="2"></textarea>
                </div>
            </div>
        `;
        
        container.appendChild(newProduct);
        productCount++;
        updateUI();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            e.target.closest('.product-item').remove();
            productCount--;
            renumberProducts();
            updateUI();
        }
    });

    function renumberProducts() {
        const items = container.querySelectorAll('.product-item');
        items.forEach((item, index) => {
            item.querySelector('h6').textContent = `Producto #${index + 1}`;
            item.querySelectorAll('input, select, textarea').forEach(input => {
                const name = input.getAttribute('name');
                if (name) {
                    input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
                }
            });
        });
    }

    function updateUI() {
        const items = container.querySelectorAll('.product-item');
        
        items.forEach((item, index) => {
            const btn = item.querySelector('.remove-product');
            btn.disabled = items.length === 1;
        });

        addBtn.disabled = productCount >= MAX_PRODUCTS;
        if (productCount >= MAX_PRODUCTS) {
            addBtn.classList.add('disabled');
        } else {
            addBtn.classList.remove('disabled');
        }
    }
});
</script>
@endsection
