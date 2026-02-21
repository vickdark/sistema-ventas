@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="purchases-create-page"></div>

    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Registrar Compra</h1>
            <p class="text-muted small">Ingrese los productos y la cantidad para registrar la compra.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        <div class="row">
            <!-- Left Side: Purchase Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">Información de la Compra</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="nro_compra" class="form-label">Número de Compra</label>
                            <input type="number" class="form-control rounded-3 bg-light" id="nro_compra" name="nro_compra" value="{{ old('nro_compra', $nextNroCompra) }}" required readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control rounded-3" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="payment_condition" class="form-label">Condición de Pago</label>
                            <select class="form-select rounded-3" id="payment_condition" name="payment_condition" onchange="toggleDueDate()">
                                <option value="cash">Contado</option>
                                <option value="credit">Crédito</option>
                            </select>
                        </div>

                        <div class="mb-3" id="dueDateContainer" style="display: none;">
                            <label for="due_date" class="form-label">Fecha de Vencimiento (Opcional)</label>
                            <input type="date" class="form-control rounded-3" id="due_date" name="due_date" value="{{ old('due_date') }}">
                            <div class="form-text small text-muted">Si es crédito y no tiene fecha exacta, puede dejarlo vacío.</div>
                        </div>

                        <div class="mb-3 d-none">
                            <label for="supplier_id_hidden" class="form-label">Proveedor ID</label>
                            <input type="hidden" id="supplier_id_hidden" name="supplier_id" required>
                        </div>

                        <div class="mb-3">
                            <label for="voucher" class="form-label">Comprobante / Factura</label>
                            <input type="text" class="form-control rounded-3" id="voucher" name="voucher" value="{{ old('voucher') }}" required placeholder="Ej: FAC-001">
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-soft rounded-4 bg-primary text-white">
                    <div class="card-body p-4">
                        <h5 class="mb-2 opacity-75">Resumen de Compra</h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Productos:</span>
                            <span id="itemsCount" class="fw-bold">0</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-4">Total:</span>
                            <span id="totalPurchase" class="fs-2 fw-bold">$ 0.00</span>
                        </div>
                        <button type="submit" class="btn btn-light btn-lg rounded-pill w-100 fw-bold" id="btnSubmit" disabled>
                            <i class="fas fa-save me-2"></i> Registrar Compra
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Side: POS Interface -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold">Buscador de Productos</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12 mb-2">
                                <label for="product_id" class="form-label">Buscar por Código o Nombre</label>
                                <!-- Native select as fallback and data source -->
                                <select id="product_id" class="form-control">
                                    <option value="" disabled selected>Escribe para buscar...</option>
                                    @foreach($products as $product)
                                        @php
                                            $firstSupplier = $product->suppliers->first();
                                        @endphp
                                        <option value="{{ $product->id }}" 
                                                data-name="{{ $product->name }}"
                                                data-code="{{ $product->code }}"
                                                data-price="{{ $product->purchase_price }}"
                                                data-max-stock="{{ $product->max_stock }}" 
                                                data-current-stock="{{ $product->stock }}"
                                                data-supplier-id="{{ $firstSupplier ? $firstSupplier->id : '' }}"
                                                data-supplier-name="{{ $firstSupplier ? $firstSupplier->name : 'Sin Proveedor' }}">
                                            {{ $product->code }} - {{ $product->name }} (Stock: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="quantity" class="form-label">Cantidad</label>
                                <input type="number" id="quantity" class="form-control rounded-3" min="1" value="1">
                            </div>
                            <div class="col-md-4">
                                <label for="price" class="form-label">Precio Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" id="price" class="form-control rounded-3" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" id="btnAddItem" class="btn btn-success rounded-pill w-100 py-2">
                                    <i class="fas fa-plus me-2"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-soft rounded-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="itemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3 border-0">Producto</th>
                                        <th class="py-3 border-0 text-center">Cantidad</th>
                                        <th class="py-3 border-0 text-center">Precio</th>
                                        <th class="py-3 border-0 text-center">Subtotal</th>
                                        <th class="px-4 py-3 border-0 text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyTableMsg">
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                                            <p class="mb-0">Busca y agrega productos para iniciar la compra</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@section('scripts')
<script>
    function toggleDueDate() {
        const condition = document.getElementById('payment_condition').value;
        const container = document.getElementById('dueDateContainer');
        if (condition === 'credit') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
            document.getElementById('due_date').value = '';
        }
    }
</script>
@endsection