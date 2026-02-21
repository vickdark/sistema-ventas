import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';
import Swal from 'sweetalert2';

export function initUsersIndex(config) {
    // Personalizar columnas para incluir acciones extra
    const customColumns = columns.map(col => {
        if (col.id === 'actions') {
            return {
                ...col,
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const isActive = row.cells[4].data; // is_active
                    const { routes } = config;
                    
                    let buttons = '<div class="btn-group">';
                    
                    // Ver
                    if (routes.show) {
                        const url = routes.show.replace(':id', id);
                        buttons += `<a href="${url}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Ver Detalles"><i class="fas fa-eye"></i></a>`;
                    }
                    
                    // Editar
                    if (routes.edit) {
                        const url = routes.edit.replace(':id', id);
                        buttons += `<a href="${url}" class="btn btn-sm btn-outline-primary rounded-pill me-1" title="Editar"><i class="fas fa-edit"></i></a>`;
                    }
                    
                    // Toggle Status
                    const toggleClass = isActive ? 'btn-outline-warning' : 'btn-outline-success';
                    const toggleIcon = isActive ? 'fa-ban' : 'fa-check';
                    const toggleTitle = isActive ? 'Desactivar usuario' : 'Activar usuario';
                    
                    buttons += `<button type="button" class="btn btn-sm ${toggleClass} rounded-pill me-1 btn-toggle-status" data-id="${id}" data-active="${isActive}" title="${toggleTitle}"><i class="fas ${toggleIcon}"></i></button>`;

                    // Eliminar
                    if (routes.destroy) {
                        const url = routes.destroy.replace(':id', id);
                        buttons += `<button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete" data-url="${url}" title="Eliminar"><i class="fas fa-trash"></i></button>`;
                    }
                    
                    buttons += '</div>';
                    
                    return window.Gridjs.html(buttons);
                }
            };
        }
        return col;
    });

    const manager = new CrudManager(config, {
        columns: customColumns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer y el usuario perderá acceso al sistema.'
        }
    });

    manager.init();

    // Event listener para el botón de toggle
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-toggle-status');
        if (btn) {
            const userId = btn.getAttribute('data-id');
            const isActive = btn.getAttribute('data-active') === 'true'; // Convertir string a boolean
            const action = isActive ? 'desactivar' : 'activar';
            
            const result = await Swal.fire({
                title: `¿${action.charAt(0).toUpperCase() + action.slice(1)} usuario?`,
                text: `El usuario ${isActive ? 'perderá acceso al sistema' : 'podrá acceder al sistema nuevamente'}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isActive ? '#d33' : '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: `Sí, ${action}`,
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed) {
                try {
                    const url = config.routes.toggle_status.replace(':id', userId);
                    const response = await axios.post(url);
                    
                    if (response.data.success) {
                        Swal.fire(
                            '¡Actualizado!',
                            response.data.message,
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    }
                } catch (error) {
                    console.error('Error al cambiar estado:', error);
                    Swal.fire('Error', 'No se pudo actualizar el estado del usuario', 'error');
                }
            }
        }
    });
}
