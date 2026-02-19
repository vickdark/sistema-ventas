import Notifications from '../../../../modules/Notifications';

export class CheckoutManager {
    constructor(config, cartManager, customerManager) {
        this.routes = config.routes;
        this.tokens = config.tokens;
        this.cartManager = cartManager;
        this.customerManager = customerManager;
        
        this.btnProcessSale = document.getElementById('btnProcessSale');
        this.init();
    }

    init() {
        if (this.btnProcessSale) {
            this.btnProcessSale.addEventListener('click', () => this.processSale());
        }
    }

    async processSale() {
        // Verificar conexión antes de procesar
        if (!navigator.onLine) {
            Notifications.error('Sin conexión', 'Se requiere conexión a internet para procesar la venta.');
            return;
        }

        const client_id = this.customerManager.getSelectedClientId();
        const payment_type = document.querySelector('input[name="payment_type"]:checked').value;
        const voucher = document.getElementById('voucher')?.value;
        const credit_payment_date = document.getElementById('credit_payment_date')?.value;

        if (!client_id) {
            Notifications.error('Cliente requerido', 'Por favor seleccione un cliente para continuar.');
            return;
        }

        if (this.cartManager.isEmpty()) return;

        const confirmed = await Notifications.confirm({
            title: '¿Confirmar venta?',
            text: `Venta por un total de $${this.cartManager.getTotal().toLocaleString(undefined, {minimumFractionDigits: 2})}`,
            confirmButtonText: 'Sí, cobrar'
        });

        if (confirmed) {
            this.setProcessing(true);

            const saleData = {
                client_id,
                payment_type,
                voucher,
                credit_payment_date,
                items: this.cartManager.getItems().map(i => ({ product_id: i.id, quantity: i.quantity })),
                total: this.cartManager.getTotal(),
                client_name: this.customerManager.getSelectedClientName()
            };

            try {
                await this.sendToServer(saleData);
            } catch (error) {
                console.error('Error enviando al servidor:', error);
                Notifications.error('Error', error.message);
                this.setProcessing(false);
            }
        }
    }

    setProcessing(isProcessing) {
        this.btnProcessSale.disabled = isProcessing;
        this.btnProcessSale.innerHTML = isProcessing 
            ? '<span class="spinner-border spinner-border-sm me-2"></span> PROCESANDO...' 
            : '<i class="fas fa-check-circle me-2"></i> PROCESAR PAGO';
    }

    async sendToServer(saleData) {
        const response = await fetch(this.routes.store, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.tokens.csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify(saleData)
        });

        const result = await response.json();

        if (response.ok) {
            Notifications.success('Venta exitosa', result.message);
            
            const printConfirmed = await Notifications.confirm({
                title: 'Venta Registrada',
                text: '¿Desea imprimir el ticket de venta?',
                confirmButtonText: '<i class="fas fa-print"></i> Imprimir Ticket',
                cancelButtonText: 'Cerrar',
                icon: 'success'
            });

            if (printConfirmed) {
                window.open(`${this.routes.index}/${result.sale_id}/ticket`, '_blank');
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } else {
            throw new Error(result.message || 'Error al procesar la venta');
        }
    }
}
