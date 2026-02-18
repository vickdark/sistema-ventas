export function initBilling() {
    const serviceTypeSelect = document.getElementById('service_type');
    const subscriptionPeriodSelect = document.getElementById('subscription_period');
    const subscriptionPeriodContainer = document.getElementById('subscription_period_container');
    const nextPaymentDateLabel = document.getElementById('next_payment_date_label');
    const nextPaymentDateInput = document.getElementById('next_payment_date');

    function calculateNextPaymentDate() {
        if (!nextPaymentDateInput) return;

        const today = new Date();
        const startDate = new Date(today);
        startDate.setDate(today.getDate() + 1);

        let daysToAdd = 0;
        if (serviceTypeSelect.value === 'subscription') {
            daysToAdd = parseInt(subscriptionPeriodSelect.value) || 30;
        } else {
            daysToAdd = 365; 
        }

        const nextDate = new Date(startDate);
        nextDate.setDate(startDate.getDate() + daysToAdd);

        const yyyy = nextDate.getFullYear();
        const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
        const dd = String(nextDate.getDate()).padStart(2, '0');
        
        nextPaymentDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    function toggleBillingFields() {
        if (!serviceTypeSelect) return;
        
        if (serviceTypeSelect.value === 'subscription') {
            subscriptionPeriodContainer.classList.remove('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Facturación';
        } else {
            subscriptionPeriodContainer.classList.add('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Cobro Mantenimiento';
        }
        calculateNextPaymentDate();
    }

    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', toggleBillingFields);
        subscriptionPeriodSelect.addEventListener('change', calculateNextPaymentDate);
        toggleBillingFields(); 
    }
}
