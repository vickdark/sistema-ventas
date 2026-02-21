import Notifications from "../../../modules/Notifications";

export function initSupplierPaymentsEdit(config) {
    const form = document.getElementById('editPaymentForm');
    if (!form) return;

    // Validación en tiempo real del monto máximo
    const amountInput = document.getElementById('payment_amount');
    if (amountInput) {
        amountInput.addEventListener('input', function() {
            const max = parseFloat(this.getAttribute('max') || 0);
            const val = parseFloat(this.value || 0);
            if (val > max) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        
        const btn = document.getElementById('btnUpdatePayment');
        const originalBtnContent = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ACTUALIZANDO...';

        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Aseguramos que el token CSRF esté presente
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            const response = await fetch(config.routes.update, {
                method: 'POST', // Usamos POST pero con _method=PUT (o PUT directo si Laravel lo acepta)
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Notifications.success(result.message || 'Abono actualizado correctamente.', 'Éxito');
                
                // Redirigir al detalle de la compra
                setTimeout(() => {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                    } else {
                        // Fallback
                        window.location.href = config.routes.index;
                    }
                }, 1000);
            } else {
                Notifications.error(result.message || 'Error al actualizar el pago.', 'Error');
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            }
        } catch (error) {
            console.error(error);
            Notifications.error('No se pudo conectar con el servidor.', 'Error');
            btn.disabled = false;
            btn.innerHTML = originalBtnContent;
        }
    };
}