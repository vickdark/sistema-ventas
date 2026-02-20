import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';
import DataGrid from '../../../modules/DataGrid';


export function initSupplierPaymentsIndex(config) {
    const wrapper = document.getElementById("wrapper");
    if (!wrapper) return;

    const modalElement = document.getElementById('paymentModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('paymentForm');

    const manager = new CrudManager(config, {
        columns: columns.map(col => {
            if (col.id === 'actions') {
                return {
                    ...col,
                    formatter: (cell, row) => {
                        const id = row.cells[0].data;
                        const pending = row.cells[4].data;
                        const total = row.cells[3].data;

                        const html = `
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill me-2 btn-pay" 
                                    data-id="${id}" data-pending="${pending}" data-total="${total}">
                                    Abonar
                                </button>
                                <a href="${config.routes.index}/${id}" class="btn btn-sm btn-outline-dark rounded-pill">
                                    Detalle
                                </a>
                            </div>
                        `;
                        return DataGrid.html(html);
                    }
                };
            }
            return col;
        }),
        mapData: mapData,
        onInit: (crud) => {
            // Personalizar el grid para actualizar el total pendiente
            const originalThen = crud.grid.options.server.then;
            crud.grid.options.server.then = (data) => {
                const totalPending = data.data.reduce((acc, curr) => acc + parseFloat(curr.pending_amount), 0);
                const label = document.getElementById('totalPendingLabel');
                if (label) label.textContent = `$${totalPending.toFixed(2)}`;
                return originalThen(data);
            };
        }
    });

    manager.init();

    // Eventos delegados
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-pay');
        if (!btn) return;

        document.getElementById('modal_purchase_id').value = btn.dataset.id;
        document.getElementById('modal_total_amount').textContent = `$${parseFloat(btn.dataset.total).toFixed(2)}`;
        document.getElementById('modal_pending_amount').textContent = `$${parseFloat(btn.dataset.pending).toFixed(2)}`;
        document.getElementById('payment_amount').value = parseFloat(btn.dataset.pending).toFixed(2);
        modal.show();
    });

    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSavePayment');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> PROCESANDO...';

            try {
                const response = await fetch(config.routes.store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.tokens.csrf || document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(Object.fromEntries(new FormData(form)))
                });

                const data = await response.json();
                if (response.ok) {
                    Notify.success('Éxito', 'Abono registrado correctamente.');
                    modal.hide();
                    form.reset();
                    window.location.reload(); 
                } else {
                    Notify.error('Error', data.message || 'Error al procesar el pago.');
                }
            } catch (error) {
                Notify.error('Error', 'No se pudo conectar con el servidor.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle me-2"></i> CONFIRMAR PAGO';
            }
        };
    }
}
