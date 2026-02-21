import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/grid';
import Notifications from '../../../modules/Notifications';

export function initStockTransfersCreate(config) {
    // Estilos específicos para el layout POS
    document.body.classList.add('pos-page');
    if (window.innerWidth > 991) {
        document.body.classList.add('sidebar-mini');
    }

    let cart = [];
    let swiper = null;
    let originalSlides = [];

    const elements = {
        productSearch: document.getElementById('productSearch'),
        productsGrid: document.getElementById('productsGrid'),
        cartItems: document.getElementById('cartItems'),
        emptyCart: document.getElementById('emptyCart'),
        btnSaveTransfer: document.getElementById('btnSaveTransfer'),
        btnClearCart: document.getElementById('btnClearCart'),
        categoryBtns: document.querySelectorAll('.category-btn'),
        template: document.getElementById('cartItemTemplate'),
        destinationBranchId: document.getElementById('destination_branch_id'),
        notes: document.getElementById('notes'),
        cartSidebar: document.getElementById('cartSidebar'),
        btnToggleCart: document.getElementById('btnToggleCart'),
        btnCloseCart: document.getElementById('btnCloseCart')
    };

    // 1. Inicialización de Swiper
    function initSwiper() {
        if (swiper) swiper.destroy(true, true);

        swiper = new Swiper('.swiper-products', {
            modules: [Navigation, Pagination, Grid],
            slidesPerView: 1,
            grid: { fill: 'row', rows: 2 },
            spaceBetween: 20,
            pagination: { el: '.swiper-pagination', clickable: true, dynamicBullets: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            breakpoints: {
                640: { slidesPerView: 2, grid: { rows: 2 } },
                1024: { slidesPerView: 3, grid: { rows: 2 } },
                1400: { slidesPerView: 4, grid: { rows: 2 } }
            },
            observer: true,
            observeParents: true,
        });
    }

    if (elements.productsGrid) {
        originalSlides = Array.from(elements.productsGrid.querySelectorAll('.swiper-slide'));
        initSwiper();
        
        // Delegación para agregar al envío
        elements.productsGrid.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-add-product');
            if (!btn) return;

            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                stock: parseInt(btn.dataset.stock)
            };
            
            if (product.stock <= 0) {
                Notifications.error('Sin stock', 'Este producto no tiene stock en esta sucursal.');
                return;
            }

            addToCart(product);
            
            // Feedback visual
            const card = btn.closest('.card');
            if (card) {
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.style.transform = '', 100);
            }
        });
    }

    // 2. Filtros y Búsqueda
    function filterProducts() {
        if (!elements.productsGrid) return;
        
        const query = elements.productSearch.value.toLowerCase();
        const activeCategory = document.querySelector('.category-btn.active')?.textContent.trim().toLowerCase();

        elements.productsGrid.innerHTML = '';

        originalSlides.forEach(slide => {
            const name = slide.dataset.name.toLowerCase();
            const code = slide.dataset.code.toLowerCase();
            const category = (slide.dataset.category || '').toLowerCase();
            
            const matchesSearch = name.includes(query) || code.includes(query);
            const matchesCategory = !activeCategory || activeCategory === 'todos los productos' || category === activeCategory;

            if (matchesSearch && matchesCategory) {
                elements.productsGrid.appendChild(slide);
            }
        });
        
        initSwiper();
    }

    if (elements.productSearch) {
        elements.productSearch.addEventListener('input', () => filterProducts());
    }

    elements.categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            elements.categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterProducts();
        });
    });

    // 3. Gestión del Carrito (Traslado)
    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            // Usamos existing.stock porque puede incluir el stock virtual en modo edición
            if (existing.quantity >= existing.stock) {
                Notifications.error('Sin stock', 'No puedes trasladar más de lo que tienes disponible.');
                return;
            }
            existing.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        renderCart();
    }

    function renderCart() {
        elements.cartItems.querySelectorAll('.cart-item-modern').forEach(el => el.remove());
        
        if (cart.length === 0) {
            elements.emptyCart.classList.remove('d-none');
            elements.btnSaveTransfer.disabled = true;
            updateCartCount();
            return;
        }

        elements.emptyCart.classList.add('d-none');
        elements.btnSaveTransfer.disabled = false;

        cart.forEach((item, index) => {
            const clone = elements.template.content.cloneNode(true);
            const row = clone.querySelector('.cart-item-modern');
            
            row.querySelector('.item-name').textContent = item.name;
            row.querySelector('.item-qty').value = item.quantity;
            row.querySelector('.item-stock-limit').textContent = `Disp: ${item.stock}`;

            row.querySelector('.btn-minus').onclick = () => updateQty(index, -1);
            row.querySelector('.btn-plus').onclick = () => updateQty(index, 1);
            row.querySelector('.btn-remove-item').onclick = () => {
                cart.splice(index, 1);
                renderCart();
            };

            elements.cartItems.appendChild(row);
        });

        updateCartCount();
    }

    function updateQty(index, delta) {
        const item = cart[index];
        if (delta > 0 && item.quantity >= item.stock) {
            Notifications.error('Stock insuficiente');
            return;
        }
        
        item.quantity += delta;
        if (item.quantity < 1) cart.splice(index, 1);
        renderCart();
    }

    function updateCartCount() {
        const count = cart.reduce((acc, item) => acc + item.quantity, 0);
        document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
    }

    // Toggles Móvil
    if (elements.btnToggleCart) {
        elements.btnToggleCart.onclick = () => elements.cartSidebar.classList.toggle('show');
    }
    if (elements.btnCloseCart) {
        elements.btnCloseCart.onclick = () => elements.cartSidebar.classList.remove('show');
    }

    // Atajos
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            elements.productSearch?.focus();
        }
    });

    // Vaciar
    if (elements.btnClearCart) {
        elements.btnClearCart.onclick = async () => {
            if (cart.length === 0) return;
            const confirmed = await Notifications.confirm({ title: '¿Vaciar envío?' });
            if (confirmed) {
                cart = [];
                renderCart();
            }
        };
    }

    // 4. Guardar Traslado
    if (elements.btnSaveTransfer) {
        elements.btnSaveTransfer.onclick = async () => {
            if (!elements.destinationBranchId.value) {
                Notifications.error('Error', 'Debes seleccionar una sucursal de destino.');
                return;
            }

            elements.btnSaveTransfer.disabled = true;
            elements.btnSaveTransfer.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESANDO...';

            const payload = {
                destination_branch_id: elements.destinationBranchId.value,
                notes: elements.notes.value,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity
                }))
            };

            const isEdit = !!config.transfer;
            const url = isEdit ? config.routes.update : config.routes.store;
            const method = isEdit ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.tokens?.csrf || document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    Notifications.success('Éxito', isEdit ? 'Traslado actualizado correctamente.' : 'Traslado enviado correctamente.');
                    window.location.href = config.routes.index;
                } else {
                    Notifications.error('Error', data.message || 'Error al procesar el traslado.');
                    elements.btnSaveTransfer.disabled = false;
                    elements.btnSaveTransfer.innerHTML = isEdit ? '<i class="fas fa-sync-alt me-2"></i> ACTUALIZAR ENVÍO' : '<i class="fas fa-paper-plane me-2"></i> CONFIRMAR ENVÍO';
                }
            } catch (error) {
                Notifications.error('Error de red', 'No se pudo conectar con el servidor.');
                elements.btnSaveTransfer.disabled = false;
                elements.btnSaveTransfer.innerHTML = isEdit ? '<i class="fas fa-sync-alt me-2"></i> ACTUALIZAR ENVÍO' : '<i class="fas fa-paper-plane me-2"></i> CONFIRMAR ENVÍO';
            }
        };
    }

    // Inicializar datos si es edición
    if (config.transfer) {
        cart = config.transfer.items.map(item => ({
            id: String(item.id), // Aseguramos que sea string para coincidencias
            name: item.name,
            stock: item.stock,
            quantity: item.quantity
        }));
        renderCart();
    }
}
