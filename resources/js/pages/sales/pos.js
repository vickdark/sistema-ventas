import Notifications from '../../modules/Notifications';

export function initSalesPOS(config) {
    const { routes, tokens } = config;
    let cart = [];

    // Elements
    const clientSelect = new TomSelect('#client_id', {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: 'Seleccione un cliente...'
    });
    const productSearch = document.getElementById('productSearch');
    const productsGrid = document.getElementById('productsGrid');
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const totalLabel = document.getElementById('totalLabel');
    const subtotalLabel = document.getElementById('subtotalLabel');
    const btnProcessSale = document.getElementById('btnProcessSale');
    const paymentRadios = document.getElementsByName('payment_type');
    const creditDateSection = document.getElementById('creditDateSection');

    // Search Logic
    productSearch.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(item => {
            const name = item.dataset.name;
            const code = item.dataset.code;
            if (name.includes(query) || code.includes(query)) {
                item.classList.remove('d-none');
            } else {
                item.classList.add('d-none');
            }
        });
    });

    // Payment Type Toggle
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (radio.value === 'CREDITO') {
                creditDateSection.classList.remove('d-none');
            } else {
                creditDateSection.classList.add('d-none');
            }
        });
    });

    // Add Product to Cart
    document.querySelectorAll('.btn-add-product').forEach(btn => {
        btn.addEventListener('click', () => {
            const product = {
                id: btn.dataset.id,
                name: btn.dataset.name,
                price: parseFloat(btn.dataset.price),
                stock: parseInt(btn.dataset.stock)
            };
            addToCart(product);
        });
    });

    function addToCart(product) {
        const existing = cart.find(item => item.id === product.id);
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
        cartItems.querySelectorAll('.cart-item-row').forEach(el => el.remove());
        
        if (cart.length === 0) {
            emptyCart.classList.remove('d-none');
            btnProcessSale.disabled = true;
        } else {
            emptyCart.classList.add('d-none');
            btnProcessSale.disabled = false;
        }

        const template = document.getElementById('cartItemTemplate');
        let total = 0;

        cart.forEach((item, index) => {
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.cart-item-row');
            
            row.querySelector('.item-name').textContent = item.name;
            row.querySelector('.item-qty').value = item.quantity;
            row.querySelector('.item-price-total').textContent = `$${(item.price * item.quantity).toLocaleString()}`;

            row.querySelector('.btn-minus').addEventListener('click', () => {
                if (item.quantity > 1) {
                    item.quantity--;
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
        });

        totalLabel.textContent = `$${total.toLocaleString()}`;
        subtotalLabel.textContent = `$${total.toLocaleString()}`;
    }

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

            try {
                const response = await fetch(routes.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        client_id,
                        payment_type,
                        voucher,
                        credit_payment_date,
                        items: cart.map(i => ({ product_id: i.id, quantity: i.quantity }))
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Venta exitosa', result.message);
                    
                    // Offer to print ticket
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
                } else {
                    Notify.error('Error', result.message || 'Error al procesar la venta');
                    btnProcessSale.disabled = false;
                    btnProcessSale.innerHTML = '<i class="fas fa-check-circle me-2"></i> PROCESAR VENTA';
                }
            } catch (error) {
                Notify.error('Error de conexión');
                console.error(error);
                btnProcessSale.disabled = false;
                btnProcessSale.innerHTML = '<i class="fas fa-check-circle me-2"></i> PROCESAR VENTA';
            }
        }
    });

    renderCart();
}
