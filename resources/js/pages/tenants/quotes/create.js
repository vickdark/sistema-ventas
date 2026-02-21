import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/grid';
import Notifications from '../../../modules/Notifications';
import { CustomerManager } from '../sales/pos/CustomerManager';

export function initQuotesCreate(config) {
    // Estilos específicos para el layout POS
    document.body.classList.add('pos-page');
    if (window.innerWidth > 991) {
        document.body.classList.add('sidebar-mini');
    }

    let cart = [];
    let swiper = null;
    let originalSlides = [];

    // Inicializar Manager de Clientes
    const customerManager = new CustomerManager(config);

    const elements = {
        productSearch: document.getElementById('productSearch'),
        productsGrid: document.getElementById('productsGrid'),
        cartItems: document.getElementById('cartItems'),
        emptyCart: document.getElementById('emptyCart'),
        totalLabel: document.getElementById('totalLabel'),
        subtotalLabel: document.getElementById('subtotalLabel'),
        btnSaveQuote: document.getElementById('btnSaveQuote'),
        btnClearCart: document.getElementById('btnClearCart'),
        categoryBtns: document.querySelectorAll('.category-btn'),
        template: document.getElementById('cartItemTemplate'),
        expirationDate: document.getElementById('expiration_date'),
        notes: document.getElementById('notes'),
        cartSidebar: document.getElementById('cartSidebar'),
        btnToggleCart: document.getElementById('btnToggleCart'),
        btnCloseCart: document.getElementById('btnCloseCart')
    };

    // Pre-cargar datos si es edición
    if (config.quote) {
        cart = config.quote.items.map(item => ({
            id: item.product_id,
            name: item.product.name,
            price: parseFloat(item.price),
            quantity: item.quantity,
            image: item.product.image
        }));

        if (elements.expirationDate && config.quote.expiration_date) {
            // Asumiendo que viene en formato Y-m-d desde el controlador
            elements.expirationDate.value = config.quote.expiration_date;
        }

        if (elements.notes) {
            elements.notes.value = config.quote.notes || '';
        }

        if (config.quote.client_id && customerManager.clientSelect) {
            customerManager.clientSelect.setValue(config.quote.client_id);
        }

        if (elements.btnSaveQuote) {
            elements.btnSaveQuote.innerHTML = '<i class="fas fa-save me-2"></i> ACTUALIZAR COTIZACIÓN';
        }
        
        // Renderizar carrito inicial
        // Necesitamos esperar a que el DOM esté listo o llamar a renderCart aquí
        // Como initQuotesCreate se llama cuando el DOM ya debería estar listo (o al final del body), debería funcionar.
        // Pero renderCart usa elements.cartItems que acabamos de definir.
        // renderCart está definida más abajo, así que necesitamos mover esta llamada después de definir renderCart 
        // o mover la definición de renderCart antes.
        // Javascript hoisting de funciones declaradas con 'function' permite llamarlas antes.
        // renderCart es una function declaration, así que debería funcionar.
        renderCart();
    }

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
        
        // Delegación para agregar al carrito
        elements.productsGrid.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-add-product');
            if (!btn) return;

            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                image: btn.dataset.image
            };
            
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

    // 3. Gestión del Carrito (Cotización)
    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id);
        if (existing) {
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
            elements.btnSaveQuote.disabled = true;
            elements.totalLabel.textContent = '$0.00';
            if (elements.subtotalLabel) elements.subtotalLabel.textContent = '$0.00';
            updateCartCount();
            return;
        }

        elements.emptyCart.classList.add('d-none');
        elements.btnSaveQuote.disabled = false;

        let total = 0;
        cart.forEach((item, index) => {
            const clone = elements.template.content.cloneNode(true);
            const row = clone.querySelector('.cart-item-modern');
            
            row.querySelector('.item-name').textContent = item.name;
            row.querySelector('.item-qty').value = item.quantity;
            row.querySelector('.item-price-unit').textContent = `$${item.price.toFixed(2)} c/u`;
            row.querySelector('.item-price-total').textContent = `$${(item.price * item.quantity).toFixed(2)}`;

            row.querySelector('.btn-minus').onclick = () => updateQty(index, -1);
            row.querySelector('.btn-plus').onclick = () => updateQty(index, 1);
            row.querySelector('.btn-remove-item').onclick = () => {
                cart.splice(index, 1);
                renderCart();
            };

            elements.cartItems.appendChild(row);
            total += item.price * item.quantity;
        });

        elements.totalLabel.textContent = `$${total.toFixed(2)}`;
        if (elements.subtotalLabel) elements.subtotalLabel.textContent = `$${total.toFixed(2)}`;
        updateCartCount();
    }

    function updateQty(index, delta) {
        cart[index].quantity += delta;
        if (cart[index].quantity < 1) cart.splice(index, 1);
        renderCart();
    }

    function updateCartCount() {
        const count = cart.reduce((acc, item) => acc + item.quantity, 0);
        const badge = document.querySelector('.cart-count');
        if (badge) badge.textContent = count;
    }

    // Toggles Móvil
    if (elements.btnToggleCart) {
        elements.btnToggleCart.onclick = () => elements.cartSidebar.classList.toggle('show');
    }
    if (elements.btnCloseCart) {
        elements.btnCloseCart.onclick = () => elements.cartSidebar.classList.remove('show');
    }

    // 4. Atajos de Teclado
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
            const confirmed = await Notifications.confirm({ title: '¿Vaciar cotización?' });
            if (confirmed) {
                cart = [];
                renderCart();
            }
        };
    }

    // 5. Guardar
    if (elements.btnSaveQuote) {
        elements.btnSaveQuote.onclick = async () => {
            elements.btnSaveQuote.disabled = true;
            elements.btnSaveQuote.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESANDO...';

            const payload = {
                client_id: customerManager.getSelectedClientId(),
                expiration_date: elements.expirationDate.value,
                notes: elements.notes.value,
                items: cart.map(item => ({
                    product_id: item.id,
                    quantity: item.quantity
                }))
            };

            try {
                const url = config.routes.store || config.routes.update;
                
                const response = await fetch(url, {
                    method: config.quote ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.tokens.csrf || document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok) {
                    Notifications.success('Éxito', 'Cotización generada correctamente.');
                    window.location.href = config.routes.index;
                } else {
                    Notifications.error('Error', data.message || 'Error al guardar la cotización.');
                    elements.btnSaveQuote.disabled = false;
                    elements.btnSaveQuote.innerHTML = '<i class="fas fa-save me-2"></i> GENERAR COTIZACIÓN';
                }
            } catch (error) {
                Notifications.error('Error de red', 'No se pudo conectar con el servidor.');
                elements.btnSaveQuote.disabled = false;
                elements.btnSaveQuote.innerHTML = '<i class="fas fa-save me-2"></i> GENERAR COTIZACIÓN';
            }
        };
    }
}
