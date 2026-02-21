import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';
import DataGrid from '../../../modules/DataGrid';


export function initSupplierPaymentsIndex(config) {
    console.log('Init Supplier Payments Index', config);
    const wrapper = document.getElementById("wrapper");
    if (!wrapper) return;

    const manager = new CrudManager(config, {
        columns: columns.map(col => {
            if (col.id === 'actions') {
                return {
                    ...col,
                    formatter: (cell, row) => {
                        const id = row.cells[0].data;
                        const pending = parseFloat(row.cells[4].data);
                        
                        let buttons = '';
                        
                        if (pending > 0.01) {
                            buttons += `
                                <a href="${config.routes.create}?purchase_id=${id}" class="btn btn-sm btn-primary rounded-pill me-2">
                                    <i class="fas fa-hand-holding-dollar"></i> Abonar
                                </a>
                            `;
                        } else {
                            buttons += `<span class="badge bg-success me-2 align-self-center"><i class="fas fa-check"></i> PAGADO</span>`;
                        }

                        buttons += `
                            <a href="${config.routes.index}/${id}" class="btn btn-sm btn-outline-dark rounded-pill">
                                <i class="fas fa-eye"></i> Detalle
                            </a>
                        `;

                        return DataGrid.html(`<div class="d-flex align-items-center">${buttons}</div>`);
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
                if (label) label.textContent = `${totalPending.toFixed(2)}`;
                return originalThen(data);
            };
        }
    });

    manager.init();
}
// (Se eliminó toda la lógica del modal)