export class PosUI {
    constructor(productSearch, checkoutManager) {
        this.productSearch = productSearch;
        this.checkoutManager = checkoutManager;
        
        this.init();
    }

    init() {
        // Estilos específicos para el POS
        document.body.classList.add('pos-page');

        // Minimizar sidebar automáticamente para el POS
        if (window.innerWidth > 991) {
            document.body.classList.add('sidebar-mini');
        }

        this.initMobileToggles();
        this.initPaymentRadios();
        this.initKeyboardShortcuts();
    }

    initMobileToggles() {
        const cartSidebar = document.getElementById('cartSidebar');
        const btnToggleCart = document.getElementById('btnToggleCart');
        const btnCloseCart = document.getElementById('btnCloseCart');

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
    }

    initPaymentRadios() {
        const paymentRadios = document.getElementsByName('payment_type');
        const creditDateSection = document.getElementById('creditDateSection');
        const cashCalculation = document.getElementById('cashCalculation');

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
    }

    initKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F2') {
                e.preventDefault();
                this.productSearch.focusSearch();
            }
            if (e.key === 'F4') {
                e.preventDefault();
                // Simular click en procesar venta
                const btn = document.getElementById('btnProcessSale');
                if (btn && !btn.disabled) btn.click();
            }
        });
    }
}
