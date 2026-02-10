import Notifications from '../../modules/Notifications';

export function initAbonosCreate(config) {
    const { routes, tokens } = config;

    const clientSelect = new TomSelect('#client_id', {
        create: false,
        placeholder: 'Seleccione un cliente...'
    });

    const saleSelect = new TomSelect('#sale_id', {
        create: false,
        placeholder: 'Abono General...'
    });

    const pendingSalesContainer = document.getElementById('pendingSalesContainer');
    const debtSummaryCards = document.getElementById('debtSummaryCards');
    const historyTableContainer = document.getElementById('historyTableContainer');
    const noClientSelected = document.getElementById('noClientSelected');
    const historyBody = document.getElementById('historyBody');
    
    const amountInput = document.getElementById('amount');
    const form = document.getElementById('abonoForm');
    const btnSubmit = document.getElementById('btnSubmit');

    // Cards Elements
    const totalInvoicedCard = document.getElementById('totalInvoicedCard');
    const totalAbonosCard = document.getElementById('totalAbonosCard');
    const totalDebtCard = document.getElementById('totalDebtCard');

    clientSelect.on('change', async (value) => {
        if (!value) {
            hideDetails();
            return;
        }
        
        loadClientDetails(value);
    });

    async function loadClientDetails(clientId) {
        try {
            // 1. Load Pending Sales
            const salesRes = await fetch(routes.pendingSales.replace(':id', clientId));
            const sales = await salesRes.json();
            
            saleSelect.clear();
            saleSelect.clearOptions();
            saleSelect.addOption({ value: '', text: 'Abono General (Distribuir en deudas)' });

            if (sales.length > 0) {
                pendingSalesContainer.classList.remove('d-none');
                sales.forEach(sale => {
                    saleSelect.addOption({
                        value: sale.id,
                        text: `Venta #${sale.nro_venta} - Pendiente: $${parseFloat(sale.remaining).toLocaleString()}`
                    });
                });
            } else {
                pendingSalesContainer.classList.add('d-none');
            }

            // 2. Load Debt Summary
            const summaryRes = await fetch(routes.summary.replace(':id', clientId));
            const summary = await summaryRes.json();
            
            totalInvoicedCard.textContent = `$${parseFloat(summary.total_invoiced).toLocaleString()}`;
            totalAbonosCard.textContent = `$${parseFloat(summary.total_abonos).toLocaleString()}`;
            totalDebtCard.textContent = `$${parseFloat(summary.total_debt).toLocaleString()}`;
            debtSummaryCards.classList.remove('d-none');

            // 3. Load Abono History
            const historyRes = await fetch(routes.history.replace(':id', clientId));
            const history = await historyRes.json();
            
            historyBody.innerHTML = '';
            if (history.length > 0) {
                history.forEach(item => {
                    const date = new Date(item.created_at).toLocaleDateString();
                    const ref = item.sale ? `Venta #${item.sale.nro_venta}` : 'Distribución General';
                    const amount = `$${parseFloat(item.amount).toLocaleString()}`;
                    
                    historyBody.innerHTML += `
                        <tr>
                            <td>${date}</td>
                            <td>${ref}</td>
                            <td class="text-end fw-bold text-primary">${amount}</td>
                        </tr>
                    `;
                });
                historyTableContainer.classList.remove('d-none');
                noClientSelected.classList.add('d-none');
            } else {
                historyTableContainer.classList.add('d-none');
                noClientSelected.classList.remove('d-none');
                noClientSelected.querySelector('p').textContent = 'El cliente no tiene abonos registrados todavía.';
            }

        } catch (error) {
            console.error(error);
            Notify.error('Error', 'No se pudieron cargar los detalles del cliente.');
        }
    }

    function hideDetails() {
        pendingSalesContainer.classList.add('d-none');
        debtSummaryCards.classList.add('d-none');
        historyTableContainer.classList.add('d-none');
        noClientSelected.classList.remove('d-none');
        noClientSelected.querySelector('p').textContent = 'Seleccione un cliente para ver su historial';
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const client_id = clientSelect.getValue();
        const sale_id = saleSelect.getValue();
        const amount = amountInput.value;

        if (!client_id || !amount) {
            Notify.error('Error', 'Debe completar los campos obligatorios.');
            return;
        }

        const confirmed = await Notify.confirm({
            title: '¿Registrar abono?',
            text: `Se registrará un abono por $${parseFloat(amount).toLocaleString()}`,
            confirmButtonText: 'Sí, registrar'
        });

        if (confirmed) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> REGISTRANDO...';

            try {
                const response = await fetch(routes.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ client_id, sale_id, amount })
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Éxito', result.message);
                    // Instead of redirecting, maybe just reload data to stay on the page
                    loadClientDetails(client_id);
                    amountInput.value = '';
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Registrar Abono Ahora';
                } else {
                    Notify.error('Error', result.message || 'Error al registrar abono');
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Registrar Abono Ahora';
                }
            } catch (error) {
                Notify.error('Error de conexión');
                console.error(error);
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fas fa-save me-2"></i> Registrar Abono Ahora';
            }
        }
    });

    // Check for client_id in URL
    const urlParams = new URLSearchParams(window.location.search);
    const clientIdParam = urlParams.get('client_id');
    if (clientIdParam) {
        clientSelect.setValue(clientIdParam);
    }
}
