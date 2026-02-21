import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';
import DataGrid from '../../../modules/DataGrid';

export function initQuotesIndex(config) {
    const manager = new CrudManager(config, {
        columns: columns.map(col => {
            if (col.id === 'actions') {
                return {
                    ...col,
                    formatter: (cell, row) => {
                        const id = row.cells[0].data;
                        const status = row.cells[3].data;
                        let html = `<div class="btn-group">
                            <a href="${config.routes.show.replace(':id', id)}" class="btn btn-sm btn-outline-info rounded-pill me-2" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>`;
                        
                        if (status === 'PENDIENTE') {
                            html += `
                                <a href="${config.routes.edit.replace(':id', id)}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-success rounded-pill btn-convert me-2" data-id="${id}" title="Convertir a Venta">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill btn-delete" data-id="${id}" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                        }
                        
                        html += '</div>';
                        return DataGrid.html(html);
                    }
                };
            }
            return col;
        }),
        mapData: mapData
    });

    manager.init();

    // Evento para convertir cotización
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-convert');
        if (!btn) return;
        
        const id = btn.dataset.id;
        if (confirm('¿Desea convertir esta cotización en una venta real?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = config.routes.convert.replace(':id', id);
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = (config.tokens && config.tokens.csrf) || document.querySelector('meta[name="csrf-token"]').content;
            
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Evento para eliminar cotización
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-delete');
        if (!btn) return;
        
        const id = btn.dataset.id;
        if (confirm('¿Estás seguro de que deseas eliminar esta cotización? Esta acción no se puede deshacer.')) {
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
