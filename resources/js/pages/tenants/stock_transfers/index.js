import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';
import DataGrid from "../../../modules/DataGrid";
import Notifications from '../../../modules/Notifications';

export function initStockTransfersIndex(config) {
    new CrudManager(config, {
        columns: columns.map(col => {
            if (col.id === 'actions') {
                return {
                    ...col,
                    formatter: (cell, row) => {
                        const id = row.cells[0].data;
                        const status = row.cells[4].data;
                        
                        let html = `<div class="btn-group">
                            <a href="${config.routes.show.replace(':id', id)}" class="btn btn-sm btn-outline-info rounded-pill me-2" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>`;
                        
                        if (status === 'ENVIADO') {
                            html += `
                                <a href="${config.routes.edit.replace(':id', id)}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete" data-id="${id}" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                        } else if (status === 'ENVIADO') { // Should be receive logic, but usually receive is done in show view or separate action
                             // Logic for receive is separate
                        }
                        
                        html += '</div>';
                        return DataGrid.html(html);
                    }
                };
            }
            return col;
        }),
        mapData: mapData
    }).init();

    // Event delegation for delete
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-delete');
        if (!btn) return;
        
        const id = btn.dataset.id;
        
        const confirmed = await Notifications.confirm({
            title: '¿Eliminar traslado?',
            text: 'Esta acción revertirá el stock a la sucursal de origen.',
            icon: 'warning',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = config.routes.destroy.replace(':id', id);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = (config.tokens && config.tokens.csrf) || document.querySelector('meta[name="csrf-token"]').content;
            
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            
            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
