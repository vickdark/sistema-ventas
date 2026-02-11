@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Editar Compra (Lote)</h1>
            <p class="text-muted small">Modifica los productos y cantidades de la compra.</p>
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

    <div id="items-data" data-items="{{ json_encode($purchaseItems) }}" style="display: none;"></div>

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')
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
                            <input type="text" class="form-control rounded-3 bg-light" id="nro_compra" name="nro_compra" value="{{ old('nro_compra', $purchase->nro_compra) }}" required readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="purchase_date" class="form-label">Fecha de Compra</label>
                            <input type="date" class="form-control rounded-3" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $purchase->purchase_date) }}" required>
                        </div>

                        <div class="mb-3 d-none">
                            <label for="supplier_id_hidden" class="form-label">Proveedor ID</label>
                            <input type="hidden" id="supplier_id_hidden" name="supplier_id" value="{{ $purchase->supplier_id }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="voucher" class="form-label">Comprobante / Factura</label>
                            <input type="text" class="form-control rounded-3" id="voucher" name="voucher" value="{{ old('voucher', $purchase->voucher) }}" required>
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
                        <button type="submit" class="btn btn-light btn-lg rounded-pill w-100 fw-bold" id="btnSubmit">
                            <i class="fas fa-sync me-2"></i> Actualizar Compra
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar items existentes de forma segura
        const itemsDataElement = document.getElementById('items-data');
        let items = JSON.parse(itemsDataElement.getAttribute('data-items') || '[]');

        let tomSelectProductInstance = null;
        
        const supplierIdHidden = document.getElementById('supplier_id_hidden');
        const nativeProductSelect = document.getElementById('product_id');
        const btnAddItem = document.getElementById('btnAddItem');
        const quantityInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');
        const itemsTableBody = document.querySelector('#itemsTable tbody');
        const itemsCountDisplay = document.getElementById('itemsCount');
        const totalPurchaseDisplay = document.getElementById('totalPurchase');
        const btnSubmit = document.getElementById('btnSubmit');

        // Renderizar items cargados inicialmente
        renderTable();

        function handleProductChange(value) {
            if (!value) {
                priceInput.value = '';
                return;
            }
            
            const option = nativeProductSelect.querySelector(`option[value="${value}"]`);
            if (option) {
                const price = option.getAttribute('data-price');
                const supplierId = option.getAttribute('data-supplier-id');
                const supplierName = option.getAttribute('data-supplier-name');

                if (!supplierId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Producto sin proveedor',
                        text: 'Este producto no tiene un proveedor asociado.'
                    });
                    return;
                }

                // Actualizar el ID del proveedor en el campo oculto si no tiene valor (primer producto)
                if (!supplierIdHidden.value) {
                    supplierIdHidden.value = supplierId;
                }
                priceInput.value = price || '';
            }
        }

        if (window.TomSelect) {
            tomSelectProductInstance = new TomSelect(nativeProductSelect, {
                create: false,
                placeholder: "Buscar por nombre, código o proveedor...",
                allowEmptyOption: true,
                sortField: { field: 'text', direction: 'asc' },
                searchField: ['text', 'name', 'code', 'supplier_name'],
                plugins: ['dropdown_input'],
                onChange: handleProductChange,
                render: {
                    option: function(data, escape) {
                        const supplierName = data.supplierName || 'Sin Proveedor';
                        const currentStock = data.currentStock || '0';
                        return `<div class="py-2 px-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold">${escape(data.text)}</span>
                                <span class="badge bg-light text-dark small">Stock: ${escape(currentStock)}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="fas fa-truck me-1"></i> Proveedor: ${escape(supplierName)}
                            </div>
                        </div>`;
                    },
                    item: function(data, escape) {
                        const supplierName = data.supplierName || 'N/A';
                        return `<div class="d-flex align-items-center">
                            <span class="fw-bold">${escape(data.text)}</span>
                            <span class="ms-2 badge bg-indigo-soft text-indigo small">Prov: ${escape(supplierName)}</span>
                        </div>`;
                    }
                }
            });
        }

        btnAddItem.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = tomSelectProductInstance ? tomSelectProductInstance.getValue() : nativeProductSelect.value;
            const quantity = parseInt(quantityInput.value);
            const price = parseFloat(priceInput.value);

            if (!productId || isNaN(quantity) || quantity <= 0 || isNaN(price) || price < 0) {
                Swal.fire('Atención', 'Verifica los datos del producto.', 'info');
                return;
            }

            const option = nativeProductSelect.querySelector(`option[value="${productId}"]`);
            const name = option.getAttribute('data-name');
            const code = option.getAttribute('data-code');
            const supplierId = option.getAttribute('data-supplier-id');
            const supplierName = option.getAttribute('data-supplier-name');

            const existingIndex = items.findIndex(item => item.product_id == productId);
            if (existingIndex > -1) {
                items[existingIndex].quantity += quantity;
                items[existingIndex].subtotal = items[existingIndex].quantity * items[existingIndex].price;
            } else {
                items.push({
                    product_id: productId,
                    name: name,
                    code: code,
                    quantity: quantity,
                    price: price,
                    subtotal: quantity * price,
                    supplier_id: supplierId,
                    supplier_name: supplierName
                });
            }

            if (tomSelectProductInstance) tomSelectProductInstance.clear();
            quantityInput.value = 1;
            priceInput.value = '';
            renderTable();
        });

        function renderTable() {
            const emptyMsg = document.getElementById('emptyTableMsg');
            if (items.length === 0) {
                emptyMsg.style.display = 'table-row';
                const rows = itemsTableBody.querySelectorAll('tr:not(#emptyTableMsg)');
                rows.forEach(row => row.remove());
                itemsCountDisplay.textContent = '0';
                totalPurchaseDisplay.textContent = '$ 0.00';
                btnSubmit.disabled = true;
                supplierIdHidden.value = '';
                return;
            }

            emptyMsg.style.display = 'none';
            const rows = itemsTableBody.querySelectorAll('tr:not(#emptyTableMsg)');
            rows.forEach(row => row.remove());

            let total = 0;
            items.forEach((item, index) => {
                total += item.subtotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-3">
                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        <div class="fw-bold">${item.name}</div>
                        <div class="small text-muted">${item.code}</div>
                        <div class="small text-info">Proveedor: ${item.supplier_name || 'N/A'}</div>
                    </td>
                    <td class="py-3 text-center" style="width: 120px;">
                        <input type="number" 
                               name="items[${index}][quantity]" 
                               class="form-control form-control-sm text-center rounded-pill" 
                               value="${item.quantity}" 
                               min="1" 
                               onchange="updateItem(${index}, 'quantity', this.value)">
                    </td>
                    <td class="py-3 text-center" style="width: 150px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-transparent border-end-0 rounded-start-pill">$</span>
                            <input type="number" 
                                   name="items[${index}][price]" 
                                   class="form-control text-center border-start-0 rounded-end-pill" 
                                   value="${item.price.toFixed(2)}" 
                                   step="0.01" 
                                   min="0" 
                                   onchange="updateItem(${index}, 'price', this.value)">
                        </div>
                    </td>
                    <td class="py-3 text-center fw-bold text-primary" style="width: 120px;">
                        $ ${item.subtotal.toFixed(2)}
                    </td>
                    <td class="px-4 py-3 text-end" style="width: 80px;">
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                itemsTableBody.appendChild(tr);
            });

            itemsCountDisplay.textContent = items.length;
            totalPurchaseDisplay.textContent = `$ ${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            btnSubmit.disabled = false;
        }

        window.updateItem = function(index, field, value) {
            if (field === 'quantity') {
                items[index].quantity = parseInt(value) || 1;
            } else if (field === 'price') {
                items[index].price = parseFloat(value) || 0;
            }
            items[index].subtotal = items[index].quantity * items[index].price;
            renderTable();
        };

        window.removeItem = function(index) {
            items.splice(index, 1);
            renderTable();
        };
    });
</script>
@endsection
