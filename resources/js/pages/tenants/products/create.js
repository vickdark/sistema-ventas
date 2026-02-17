export function initProductsCreate(config) {
    // Inicializar TomSelect para proveedores
    const supplierSelect = new TomSelect('#supplier_ids', {
        plugins: ['remove_button'],
        create: false,
        placeholder: 'Busca y selecciona proveedores...',
        render: {
            option: function(data, escape) {
                return '<div><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
            },
            item: function(data, escape) {
                return '<div title="' + escape(data.text) + '"><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
            }
        }
    });

    const container = document.getElementById('productsContainer');
    const addBtn = document.getElementById('addProduct');
    
    if (!container || !addBtn) return;

    let productCount = 1;
    const MAX_PRODUCTS = 5;

    // Obtener las categorías disponibles desde la configuración
    const categories = config.categories || [];
    
    // Generar opciones de categoría
    const categoryOptions = `
        <option value="" selected disabled>Selecciona una categoría</option>
        ${categories.map(category => `<option value="${category.id}">${category.name}</option>`).join('')}
    `;

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
