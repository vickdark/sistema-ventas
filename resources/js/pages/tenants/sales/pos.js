import Notifications from '../../../modules/Notifications';
import { saveOfflineSale } from '../../../modules/OfflineDB';
import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/grid';

export function initSalesPOS(config) {
    // Estilos específicos para el POS
    document.body.classList.add('pos-page');

    // Minimizar sidebar automáticamente para el POS
    if (window.innerWidth > 991) {
        document.body.classList.add('sidebar-mini');
    }

    const { routes, tokens } = config;
    let cart = [];
    let saleTotal = 0;

    // Elements
    const clientSelect = new TomSelect('#client_id', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: 'Seleccione cliente...'
    });
    
    const productSearch = document.getElementById('productSearch');
    const productsGrid = document.getElementById('productsGrid');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const totalLabel = document.getElementById('totalLabel');
    const subtotalLabel = document.getElementById('subtotalLabel');
    const btnProcessSale = document.getElementById('btnProcessSale');
    const btnClearCart = document.getElementById('btnClearCart');
    const paymentRadios = document.getElementsByName('payment_type');
    const creditDateSection = document.getElementById('creditDateSection');
    const cashCalculation = document.getElementById('cashCalculation');
    const receivedAmountInput = document.getElementById('received_amount');
    const changeLabel = document.getElementById('changeLabel');

    // Mobile Elements
    const cartSidebar = document.getElementById('cartSidebar');
    const btnToggleCart = document.getElementById('btnToggleCart');
    const btnCloseCart = document.getElementById('btnCloseCart');
    const cartCountBadge = document.querySelector('.cart-count');

    // Quick Client Elements
    const btnSaveQuickClient = document.getElementById('btnSaveQuickClient');
    const quickClientForm = document.getElementById('quickClientForm');
    const quickClientModal = document.getElementById('quickClientModal') ? new bootstrap.Modal(document.getElementById('quickClientModal')) : null;
    const btnOpenQuickClient = document.querySelector('[title="Nuevo Cliente"]');
    
    // Initialize Swiper
    let productSwiper = null;
    let originalSlides = [];

    const initSwiper = () => {
        if (productSwiper) {
            productSwiper.destroy(true, true);
        }

        productSwiper = new Swiper('.swiper-products', {
            modules: [Navigation, Pagination, Grid],
            slidesPerView: 1,
            grid: {
                fill: 'row',
                rows: 2,
            },
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: { slidesPerView: 2, grid: { rows: 2 } },
                1024: { slidesPerView: 3, grid: { rows: 2 } },
                1400: { slidesPerView: 4, grid: { rows: 2 } }
            },
            observer: true,
            observeParents: true,
        });
    };

    // Store original slides
    if (productsGrid) {
        originalSlides = Array.from(productsGrid.querySelectorAll('.swiper-slide'));
    }

    // Search Logic
    const filterProducts = () => {
        const query = productSearch.value.toLowerCase();
        const activeCategory = document.querySelector('.category-btn.active')?.textContent.trim().toLowerCase();

        // Clear the grid
        productsGrid.innerHTML = '';

        // Filter and append matching slides
        originalSlides.forEach(slide => {
            const name = slide.dataset.name.toLowerCase();
            const code = slide.dataset.code.toLowerCase();
            const category = (slide.dataset.category || '').toLowerCase();
            
            const matchesSearch = name.includes(query) || code.includes(query);
            const matchesCategory = activeCategory === 'todos los productos' || category === activeCategory;

            if (matchesSearch && matchesCategory) {
                productsGrid.appendChild(slide);
            }
        });
        
        // Re-initialize Swiper
        initSwiper();
        if (productSwiper) {
            productSwiper.update();
            productSwiper.slideTo(0);
        }
    };

    productSearch.addEventListener('input', filterProducts);

    // Category Buttons Logic
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterProducts();
        });
    });

    // Mobile Cart Toggles
    if (btnToggleCart) {
        btnToggleCart.addEventListener('click', () => {
            cartSidebar.classList.toggle('show');
        });
    }

    if (btnCloseCart) {
        btnCloseCart.addEventListener('click', () => {
            cartSidebar.classList.remove('show');
        });
    }

    // Payment Type Toggle
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'CREDITO') {
                creditDateSection.classList.remove('d-none');
                cashCalculation.classList.add('d-none');
            } else if (radio.value === 'CONTADO') {
                creditDateSection.classList.add('d-none');
                cashCalculation.classList.remove('d-none');
            } else {
                creditDateSection.classList.add('d-none');
                cashCalculation.classList.add('d-none');
            }
        });
    });

    // Change Calculation
    receivedAmountInput.addEventListener('input', calculateChange);

    function calculateChange() {
        const received = parseFloat(receivedAmountInput.value) || 0;
        const change = received - saleTotal;
        
        if (changeLabel) {
            changeLabel.textContent = `$${(change > 0 ? change : 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            changeLabel.className = change >= 0 ? 'h5 fw-bold text-success mb-0' : 'h5 fw-bold text-danger mb-0';
        }
    }

    // Add Product to Cart (Using delegation to avoid multiple listener issues)
    productsGrid.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-add-product');
        if (!btn) return;

        const product = {
            id: btn.dataset.id,
            name: btn.dataset.name,
            price: parseFloat(btn.dataset.price),
            stock: parseInt(btn.dataset.stock),
            image: btn.dataset.image
        };
        addToCart(product);
        
        // Visual feedback
        const card = btn.classList.contains('card') ? btn : btn.querySelector('.card');
        if (card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.style.transform = '', 100);
        }
    });

    function addToCart(product) {
        // Ensure comparison is robust by converting both to String
        const existing = cart.find(item => String(item.id) === String(product.id));
        
        if (existing) {
            if (existing.quantity >= product.stock) {
                Notify.error('Stock insuficiente', `No hay más stock disponible para ${product.name}`);
                return;
            }
            existing.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }
        renderCart();
    }

    function renderCart() {
        cartItems.querySelectorAll('.cart-item-modern').forEach(el => el.remove());
        
        if (cart.length === 0) {
            emptyCart.classList.remove('d-none');
            btnProcessSale.disabled = true;
            saleTotal = 0;
        } else {
            emptyCart.classList.add('d-none');
            btnProcessSale.disabled = false;
        }

        const template = document.getElementById('cartItemTemplate');
        let total = 0;
        let itemCount = 0;

        cart.forEach((item, index) => {
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.cart-item-modern');
            
            row.querySelector('.item-name').textContent = item.name;
            
            // Image handling in cart
            const imgEl = row.querySelector('.cart-item-img');
            const placeholderEl = row.querySelector('.cart-item-icon-placeholder');
            if (item.image) {
                imgEl.src = item.image;
                imgEl.alt = item.name;
                imgEl.classList.remove('d-none');
                placeholderEl.classList.add('d-none');
            }

            row.querySelector('.item-qty').value = item.quantity;
            row.querySelector('.item-price-unit').textContent = `$${item.price.toLocaleString()} c/u`;
            row.querySelector('.item-price-total').textContent = `$${(item.price * item.quantity).toLocaleString(undefined, {minimumFractionDigits: 2})}`;

            row.querySelector('.btn-minus').addEventListener('click', () => {
                if (item.quantity > 1) {
                    item.quantity--;
                    renderCart();
                } else {
                    cart.splice(index, 1);
                    renderCart();
                }
            });

            row.querySelector('.btn-plus').addEventListener('click', () => {
                if (item.quantity < item.stock) {
                    item.quantity++;
                    renderCart();
                } else {
                    Notify.error('Stock insuficiente');
                }
            });

            row.querySelector('.btn-remove-item').addEventListener('click', () => {
                cart.splice(index, 1);
                renderCart();
            });

            cartItems.appendChild(row);
            total += item.price * item.quantity;
            itemCount += item.quantity;
        });

        // Update mobile cart count
        if (cartCountBadge) {
            cartCountBadge.textContent = itemCount;
            cartCountBadge.classList.toggle('d-none', itemCount === 0);
        }

        saleTotal = total;
        totalLabel.textContent = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        subtotalLabel.textContent = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        calculateChange();
    }

    // Clear Cart
    btnClearCart.addEventListener('click', async () => {
        if (cart.length === 0) return;
        
        const confirmed = await Notify.confirm({
            title: '¿Vaciar carrito?',
            text: 'Se eliminarán todos los productos seleccionados.',
            confirmButtonText: 'Sí, vaciar',
            confirmButtonColor: '#dc3545'
        });

        if (confirmed) {
            cart = [];
            renderCart();
        }
    });

    // Quick Client Logic
    if (btnOpenQuickClient && quickClientModal) {
        btnOpenQuickClient.addEventListener('click', () => quickClientModal.show());
    }

    if (btnSaveQuickClient) {
        btnSaveQuickClient.addEventListener('click', async () => {
            const formData = new FormData(quickClientForm);
            const data = Object.fromEntries(formData.entries());

            if (!data.name || !data.nit_ci) {
                Notify.error('Campos requeridos', 'Nombre y NIT/CI son obligatorios.');
                return;
            }

            btnSaveQuickClient.disabled = true;
            btnSaveQuickClient.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> GUARDANDO...';

            try {
                const response = await fetch(routes.clients_store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Cliente creado', 'El cliente fue registrado exitosamente.');
                    
                    // Add to TomSelect and select it
                    clientSelect.addOption({ value: result.data.id, text: `${result.data.name} (${result.data.nit_ci})` });
                    clientSelect.setValue(result.data.id);
                    
                    quickClientModal.hide();
                    quickClientForm.reset();
                } else {
                    Notify.error('Error', result.message || 'No se pudo crear el cliente.');
                }
            } catch (error) {
                Notify.error('Error de conexión');
                console.error(error);
            } finally {
                btnSaveQuickClient.disabled = false;
                btnSaveQuickClient.innerHTML = 'Guardar Cliente';
            }
        });
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            productSearch.focus();
        }
        if (e.key === 'F4') {
            e.preventDefault();
            btnProcessSale.click();
        }
    });

    // Process Sale
    btnProcessSale.addEventListener('click', async () => {
        const client_id = clientSelect.getValue();
        const payment_type = document.querySelector('input[name="payment_type"]:checked').value;
        const voucher = document.getElementById('voucher').value;
        const credit_payment_date = document.getElementById('credit_payment_date').value;

        if (!client_id) {
            Notify.error('Cliente requerido', 'Por favor seleccione un cliente para continuar.');
            return;
        }

        if (cart.length === 0) return;

        const confirmed = await Notify.confirm({
            title: '¿Confirmar venta?',
            text: `Venta por un total de ${totalLabel.textContent}`,
            confirmButtonText: 'Sí, cobrar'
        });

        if (confirmed) {
            btnProcessSale.disabled = true;
            btnProcessSale.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...';

            const saleData = {
                client_id,
                payment_type,
                voucher,
                credit_payment_date,
                items: cart.map(i => ({ product_id: i.id, quantity: i.quantity })),
                total: saleTotal,
                client_name: clientSelect.getItem(client_id)?.textContent || 'Consumidor Final'
            };

            // Intentar enviar al servidor si hay conexión
            if (navigator.onLine) {
                try {
                    const response = await fetch(routes.store, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': tokens.csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(saleData)
                    });

                    const result = await response.json();

                    if (response.ok) {
                        Notify.success('Venta exitosa', result.message);
                        
                        const printConfirmed = await Notify.confirm({
                            title: 'Venta Registrada',
                            text: '¿Desea imprimir el ticket de venta?',
                            confirmButtonText: '<i class="fas fa-print"></i> Imprimir Ticket',
                            cancelButtonText: 'Cerrar',
                            icon: 'success'
                        });

                        if (printConfirmed) {
                            window.open(`${routes.index}/${result.sale_id}/ticket`, '_blank');
                        }
                        
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                        return;
                    } else {
                        throw new Error(result.message || 'Error al procesar la venta');
                    }
                } catch (error) {
                    console.error('Error enviando al servidor, intentando guardar localmente:', error);
                    // Si falla el servidor pero es por red, seguimos al guardado offline
                    if (!navigator.onLine || error.message.includes('Failed to fetch')) {
                        await handleOfflineSale(saleData);
                    } else {
                        Notify.error('Error', error.message);
                        btnProcessSale.disabled = false;
                        btnProcessSale.innerHTML = '<i class="fas fa-check-circle me-2"></i> PROCESAR PAGO';
                    }
                }
            } else {
                // Estamos offline directamente
                await handleOfflineSale(saleData);
            }
        }
    });

    async function handleOfflineSale(saleData) {
        try {
            await saveOfflineSale(saleData);
            Notify.success('Venta guardada localmente', 'La venta se sincronizará cuando recuperes la conexión.');
            
            // Simular éxito para el usuario
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } catch (err) {
            Notify.error('Error al guardar localmente', err.message);
            btnProcessSale.disabled = false;
            btnProcessSale.innerHTML = '<i class="fas fa-check-circle me-2"></i> PROCESAR PAGO';
        }
    }

    renderCart();
    initSwiper();
}
