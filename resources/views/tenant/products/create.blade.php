@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="products-create-page" data-config="{{ json_encode(['categories' => $categories, 'suppliers' => $suppliers], JSON_HEX_APOS) }}"></div>

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
                    <form action="{{ route('products.store') }}" method="POST" id="productsForm" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Productos a Registrar</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill shadow-sm" id="addProduct">
                                <i class="fas fa-plus me-1"></i> Agregar Otro (Máx. 5)
                            </button>
                        </div>

                        @if($suppliers->isEmpty())
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atención:</strong> No hay proveedores registrados en el sistema. 
                                Para registrar productos, primero debes <a href="{{ route('suppliers.create') }}" class="alert-link">crear un proveedor</a>.
                            </div>
                        @endif

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
                                    <div class="col-md-12 mb-4">
                                        <div class="p-3 border rounded-3 bg-white">
                                            <label class="form-label fw-bold text-primary mb-2">
                                                <i class="fas fa-truck-loading me-1"></i> Proveedores del Producto (Requerido)
                                            </label>
                                            <select class="form-select supplier-select" name="products[0][supplier_ids][]" multiple required placeholder="Busca y selecciona proveedores...">
                                                @foreach($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }} {{ $supplier->company ? '('.$supplier->company.')' : '' }}</option>
                                                @endforeach
                                            </select>
                                            <div class="form-text small mt-1">Selecciona los proveedores específicos para este producto.</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Código</label>
                                        <input type="text" class="form-control rounded-3" name="products[0][code]" required>
                                    </div>
                                    <div class="col-md-5 mb-3">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control rounded-3" name="products[0][name]" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Categoría</label>
                                        <select class="form-select rounded-3 category-select" name="products[0][category_id]" required>
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
                                        <label class="form-label">Stock Inicial (Opcional)</label>
                                        <input type="number" class="form-control rounded-3" name="products[0][stock]" placeholder="0">
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
                                        <label class="form-label">Imagen del Producto</label>
                                        <input type="file" class="form-control rounded-3" name="products[0][image]" accept="image/*">
                                        <div class="form-text small opacity-75">
                                            El archivo debe ser una imagen.<br>
                                            Formatos permitidos: jpeg, png, jpg, gif.
                                        </div>
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

@endsection
