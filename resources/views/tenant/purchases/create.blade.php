@extends('layouts.app')

@section('content')
<div class="container-fluid">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let items = [];
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

        // Function to handle product change
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
                        text: 'Este producto no tiene un proveedor asociado en la base de datos.'
                    });
                    return;
                }

                // Actualizar el ID del proveedor en el campo oculto si no tiene valor (primer producto)
                if (supplierIdHidden && !supplierIdHidden.value) {
                    supplierIdHidden.value = supplierId;
                }
                
                priceInput.value = price || '';
            }
        }

        // Initialize TomSelect for Products
        if (window.TomSelect) {
            console.log("Inicializando TomSelect para productos...");
            tomSelectProductInstance = new TomSelect(nativeProductSelect, {
                create: false,
                placeholder: "Buscar por nombre, código o proveedor...",
                allowEmptyOption: true,
                sortField: { field: 'text', direction: 'asc' },
                searchField: ['text', 'name', 'code', 'supplier_name'],
                dataAttr: 'data-ts', // Usar un atributo de datos personalizado si fuera necesario, pero por defecto TomSelect lee data-*
                plugins: ['dropdown_input'],
                onChange: handleProductChange,
                render: {
                    option: function(data, escape) {
                        // TomSelect mapea automáticamente data-supplier-name a data.supplierName
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
        } else {
            console.error("TomSelect no disponible.");
            nativeProductSelect.addEventListener('change', (e) => handleProductChange(e.target.value));
        }

        btnAddItem.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = tomSelectProductInstance ? tomSelectProductInstance.getValue() : nativeProductSelect.value;
            if (!productId) {
                Swal.fire('Atención', 'Selecciona un producto.', 'info');
                return;
            }

            const option = nativeProductSelect.querySelector(`option[value="${productId}"]`);
            if (!option) return;

            const selectedSupplierId = option.getAttribute('data-supplier-id');
            const selectedSupplierName = option.getAttribute('data-supplier-name');

            if (!selectedSupplierId) {
                Swal.fire('Error', 'El producto no tiene proveedor.', 'error');
                return;
            }

            const quantity = parseInt(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;

            if (quantity <= 0) {
                Swal.fire('Atención', 'Cantidad inválida.', 'warning');
                return;
            }

            const name = option.getAttribute('data-name');
            const code = option.getAttribute('data-code');
            const maxStock = parseInt(option.getAttribute('data-max-stock')) || 0;
            const currentStock = parseInt(option.getAttribute('data-current-stock')) || 0;

             if (maxStock > 0 && (currentStock + quantity) > maxStock) {
                Swal.fire('Límite de Stock', `Supera el máximo (${maxStock}).`, 'warning');
                return;
            }

            const existingIndex = items.findIndex(i => i.product_id == productId);
            if (existingIndex !== -1) {
                items[existingIndex].quantity += quantity;
                items[existingIndex].price = price; 
                items[existingIndex].subtotal = items[existingIndex].quantity * price;
            } else {
                items.push({
                    product_id: productId,
                    name: name,
                    code: code,
                    quantity: quantity,
                    price: price,
                    subtotal: quantity * price,
                    supplier_id: selectedSupplierId,
                    supplier_name: selectedSupplierName
                });
            }

            updateTable();
            
            if (tomSelectProductInstance) {
                tomSelectProductInstance.clear();
            } else {
                nativeProductSelect.value = "";
            }
            quantityInput.value = 1;
            priceInput.value = '';
        });

        function updateTable() {
            itemsTableBody.innerHTML = '';
            
            if (items.length === 0) {
                itemsTableBody.innerHTML = `
                    <tr id="emptyTableMsg">
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0">Agrega productos para comenzar</p>
                        </td>
                    </tr>
                `;
                btnSubmit.disabled = true;
                itemsCountDisplay.textContent = '0';
                totalPurchaseDisplay.textContent = '$ 0.00';
                
                if (supplierIdHidden) {
                    supplierIdHidden.value = '';
                }
                return;
            }

            btnSubmit.disabled = false;
            let total = 0;

            items.forEach((item, index) => {
                total += item.subtotal;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="px-4 py-3">
                        <div class="fw-bold">${item.name}</div>
                        <div class="small text-muted">${item.code}</div>
                        <div class="small text-info">Proveedor: ${item.supplier_name || 'N/A'}</div>
                        <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                        <input type="hidden" name="items[${index}][supplier_id]" value="${item.supplier_id}">
                    </td>
                    <td class="py-3 text-center">
                        <input type="number" name="items[${index}][quantity]" value="${item.quantity}" class="form-control form-control-sm mx-auto text-center rounded-pill" style="width: 80px;" onchange="window.updateItem(${index}, 'quantity', this.value)">
                    </td>
                    <td class="py-3 text-center">
                        <input type="number" step="0.01" name="items[${index}][price]" value="${item.price}" class="form-control form-control-sm mx-auto text-center rounded-pill" style="width: 100px;" onchange="window.updateItem(${index}, 'price', this.value)">
                    </td>
                    <td class="py-3 text-center fw-bold">$ ${item.subtotal.toFixed(2)}</td>
                    <td class="px-4 py-3 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="window.removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                itemsTableBody.appendChild(row);
            });

            itemsCountDisplay.textContent = items.length;
            totalPurchaseDisplay.textContent = `$ ${total.toFixed(2)}`;
        }

        window.updateItem = function(index, field, value) {
            const val = parseFloat(value) || 0;
            if (val < 0) return;
            
            if (field === 'quantity') {
                items[index].quantity = parseInt(val) || 1;
            } else if (field === 'price') {
                items[index].price = val;
            }
            
            items[index].subtotal = items[index].quantity * items[index].price;
            updateTable();
        };

        window.removeItem = function(index) {
            items.splice(index, 1);
            updateTable();
        };
    });
</script>

@endsection