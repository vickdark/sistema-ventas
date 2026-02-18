import Notifications from '../../../../modules/Notifications';

export class CartManager {
    constructor() {
        this.cart = [];
        this.saleTotal = 0;
        
        // DOM Elements
        this.cartItems = document.getElementById('cartItems');
        this.emptyCart = document.getElementById('emptyCart');
        this.totalLabel = document.getElementById('totalLabel');
        this.subtotalLabel = document.getElementById('subtotalLabel');
        this.btnProcessSale = document.getElementById('btnProcessSale');
        this.cartCountBadge = document.querySelector('.cart-count');
        this.receivedAmountInput = document.getElementById('received_amount');
        this.changeLabel = document.getElementById('changeLabel');
        this.template = document.getElementById('cartItemTemplate');
        
        this.initEvents();
    }

    initEvents() {
        // Change Calculation
        if (this.receivedAmountInput) {
            this.receivedAmountInput.addEventListener('input', () => this.calculateChange());
        }

        // Clear Cart
        const btnClearCart = document.getElementById('btnClearCart');
        if (btnClearCart) {
            btnClearCart.addEventListener('click', () => this.clearCartWithConfirmation());
        }
    }

    addToCart(product) {
        // Ensure comparison is robust by converting both to String
        const existing = this.cart.find(item => String(item.id) === String(product.id));
        
        if (existing) {
            if (existing.quantity >= product.stock) {
                Notifications.error('Stock insuficiente', `No hay más stock disponible para ${product.name}`);
                return;
            }
            existing.quantity++;
        } else {
            this.cart.push({ ...product, quantity: 1 });
        }
        this.render();
    }

    removeFromCart(index) {
        this.cart.splice(index, 1);
        this.render();
    }

    updateQuantity(index, delta) {
        const item = this.cart[index];
        if (!item) return;

        const newQty = item.quantity + delta;

        if (newQty > item.stock) {
            Notifications.error('Stock insuficiente');
            return;
        }

        if (newQty < 1) {
            this.removeFromCart(index);
        } else {
            item.quantity = newQty;
            this.render();
        }
    }

    async clearCartWithConfirmation() {
        if (this.cart.length === 0) return;
        
        const confirmed = await Notifications.confirm({
            title: '¿Vaciar carrito?',
            text: 'Se eliminarán todos los productos seleccionados.',
            confirmButtonText: 'Sí, vaciar',
            confirmButtonColor: '#dc3545'
        });

        if (confirmed) {
            this.cart = [];
            this.render();
        }
    }

    calculateChange() {
        const received = parseFloat(this.receivedAmountInput?.value) || 0;
        const change = received - this.saleTotal;
        
        if (this.changeLabel) {
            this.changeLabel.textContent = `$${(change > 0 ? change : 0).toLocaleString(undefined, {minimumFractionDigits: 2})}`;
            this.changeLabel.className = change >= 0 ? 'h5 fw-bold text-success mb-0' : 'h5 fw-bold text-danger mb-0';
        }
    }

    getItems() {
        return this.cart;
    }

    getTotal() {
        return this.saleTotal;
    }

    isEmpty() {
        return this.cart.length === 0;
    }

    render() {
        // Clean existing items
        if (this.cartItems) {
            this.cartItems.querySelectorAll('.cart-item-modern').forEach(el => el.remove());
        }
        
        if (this.cart.length === 0) {
            this.emptyCart?.classList.remove('d-none');
            if (this.btnProcessSale) this.btnProcessSale.disabled = true;
            this.saleTotal = 0;
        } else {
            this.emptyCart?.classList.add('d-none');
            if (this.btnProcessSale) this.btnProcessSale.disabled = false;
        }

        if (!this.template) return;

        let total = 0;
        let itemCount = 0;

        this.cart.forEach((item, index) => {
            const clone = this.template.content.cloneNode(true);
            const row = clone.querySelector('.cart-item-modern');
            
            row.querySelector('.item-name').textContent = item.name;
            
            // Image handling
            const imgEl = row.querySelector('.cart-item-img');
            const placeholderEl = row.querySelector('.cart-item-icon-placeholder');
            if (item.image && imgEl) {
                imgEl.src = item.image;
                imgEl.alt = item.name;
                imgEl.classList.remove('d-none');
                if (placeholderEl) placeholderEl.classList.add('d-none');
            }

            row.querySelector('.item-qty').value = item.quantity;
            row.querySelector('.item-price-unit').textContent = `$${item.price.toLocaleString()} c/u`;
            row.querySelector('.item-price-total').textContent = `$${(item.price * item.quantity).toLocaleString(undefined, {minimumFractionDigits: 2})}`;

            // Event Listeners for buttons
            row.querySelector('.btn-minus').addEventListener('click', () => this.updateQuantity(index, -1));
            row.querySelector('.btn-plus').addEventListener('click', () => this.updateQuantity(index, 1));
            row.querySelector('.btn-remove-item').addEventListener('click', () => this.removeFromCart(index));

            this.cartItems.appendChild(row);
            total += item.price * item.quantity;
            itemCount += item.quantity;
        });

        // Update mobile cart count
        if (this.cartCountBadge) {
            this.cartCountBadge.textContent = itemCount;
            this.cartCountBadge.classList.toggle('d-none', itemCount === 0);
        }

        this.saleTotal = total;
        
        if (this.totalLabel) this.totalLabel.textContent = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        if (this.subtotalLabel) this.subtotalLabel.textContent = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        
        this.calculateChange();
    }
}
