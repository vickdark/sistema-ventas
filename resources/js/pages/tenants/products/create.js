export function initProductsCreate(config) {
    const container = document.getElementById('productsContainer');
    const addBtn = document.getElementById('addProduct');
    
    if (!container || !addBtn) return;

    let productCount = 1;
    const MAX_PRODUCTS = 5;

    // Obtener configuración
    const categories = config.categories || [];
    const suppliers = config.suppliers || [];
    
    // Generar opciones de categoría
    const categoryOptions = `
        <option value="" selected disabled>Selecciona una categoría</option>
        ${categories.map(category => `<option value="${category.id}">${category.name}</option>`).join('')}
    `;

    // Generar opciones de proveedores
    const supplierOptions = suppliers.map(supplier => 
        `<option value="${supplier.id}">${supplier.name} ${supplier.company ? '(' + supplier.company + ')' : ''}</option>`
    ).join('');

    // Función para inicializar TomSelect en selector de proveedores
    const initSupplierSelect = (element) => {
        if (!element) return;
        new TomSelect(element, {
            plugins: ['remove_button'],
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: 'Busca y selecciona proveedores...',
            dropdownParent: 'body', // Asegura que el dropdown no se corte
            render: {
                option: function(data, escape) {
                    return '<div><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    return '<div title="' + escape(data.text) + '"><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                }
            }
        });
    };

    // Función para inicializar TomSelect en selector de categorías
    const initCategorySelect = (element) => {
        if (!element) return;
        new TomSelect(element, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: 'Selecciona una categoría',
            dropdownParent: 'body', // Asegura que el dropdown no se corte
            render: {
                option: function(data, escape) {
                    return '<div><i class="fas fa-tag me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    return '<div title="' + escape(data.text) + '"><i class="fas fa-tag me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                }
            }
        });
    };

    // Inicializar selectores existentes
    document.querySelectorAll('.category-select').forEach(initCategorySelect);
    document.querySelectorAll('.supplier-select').forEach(initSupplierSelect);

    addBtn.addEventListener('click', function() {
        if (productCount >= MAX_PRODUCTS) {
            alert('Solo puedes agregar hasta 5 productos a la vez.');
            return;
        }

        const newProduct = document.createElement('div');
        newProduct.className = 'product-item mb-4 p-4 border rounded-3 bg-light';
        
        // Fecha actual en formato YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];

        newProduct.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">Producto #${productCount + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-product product-remove-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="p-3 border rounded-3 bg-white">
                        <label class="form-label fw-bold text-primary mb-2">
                            <i class="fas fa-truck-loading me-1"></i> Proveedores del Producto (Requerido)
                        </label>
                        <select class="form-select supplier-select" name="products[${productCount}][supplier_ids][]" multiple required placeholder="Busca y selecciona proveedores...">
                            ${supplierOptions}
                        </select>
                        <div class="form-text small mt-1">Selecciona los proveedores específicos para este producto.</div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Código</label>
                    <input type="text" class="form-control rounded-3" name="products[${productCount}][code]" required>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control rounded-3" name="products[${productCount}][name]" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Categoría</label>
                    <select class="form-select rounded-3 category-select" name="products[${productCount}][category_id]" required>
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
                    <label class="form-label">Stock Inicial (Opcional)</label>
                    <input type="number" class="form-control rounded-3" name="products[${productCount}][stock]" placeholder="0">
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
                    <input type="date" class="form-control rounded-3" name="products[${productCount}][entry_date]" value="${today}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Imagen del Producto</label>
                    <input type="file" class="form-control rounded-3" name="products[${productCount}][image]" accept="image/*">
                    <div class="form-text small opacity-75">
                        El archivo debe ser una imagen.<br>
                        Formatos permitidos: jpeg, png, jpg, gif.
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Descripción (Opcional)</label>
                    <textarea class="form-control rounded-3" name="products[${productCount}][description]" rows="2"></textarea>
                </div>
            </div>
        `;
        
        container.appendChild(newProduct);
        
        // Inicializar TomSelect en los nuevos selectores
        const newCategorySelect = newProduct.querySelector('.category-select');
        initCategorySelect(newCategorySelect);
        
        const newSupplierSelect = newProduct.querySelector('.supplier-select');
        initSupplierSelect(newSupplierSelect);

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
}
