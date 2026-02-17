import Swal from 'sweetalert2';

export function initPurchasesCreate() {
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

    if (!nativeProductSelect || !btnAddItem) return;

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
}
