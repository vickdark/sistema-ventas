import DataGrid from './DataGrid';
import Notifications from './Notifications';

/**
 * Gestor centralizado para operaciones CRUD en el frontend.
 * Encapsula la lógica de Grid.js y manejo de acciones comunes.
 */
export default class CrudManager {
    /**
     * @param {Object} config - Configuración del servidor (routes, tokens)
     * @param {Object} options - Opciones del módulo (columns, mapData, deleteMessage, etc.)
     */
    constructor(config, options) {
        this.config = config;
        this.options = {
            containerId: 'wrapper',
            deleteMessage: {
                title: '¿Eliminar registro?',
                text: 'Esta acción no se puede deshacer.'
            },
            ...options
        };
        
        this.grid = null;
    }

    /**
     * Inicializa el componente CRUD
     */
    init() {
        this.initGrid();
        this.initDeleteHandler();
        
        // Hook para inicializaciones extra si se requiere
        if (this.options.onInit) {
            this.options.onInit(this);
        }
    }

    /**
     * Configura e inicializa el DataGrid
     */
    initGrid() {
        const { routes } = this.config;
        
        // Procesar columnas para inyectar acciones si es necesario
        const columns = this.options.columns.map(col => {
            if (col.id === 'actions' && !col.formatter) {
                // Si hay columna 'actions' sin formatter, usar el por defecto
                return {
                    ...col,
                    formatter: (cell, row) => this.renderDefaultActions(row.cells[0].data)
                };
            }
            return col;
        });

        this.grid = new DataGrid(this.options.containerId, {
            url: routes.index,
            columns: columns,
            mapData: this.options.mapData,
            ...this.options.gridOptions
        }).render();
    }

    /**
     * Genera el HTML para los botones de acción estándar
     */
    renderDefaultActions(id) {
        const { routes } = this.config;
        let buttons = '<div class="btn-group">';
        
        if (routes.show) {
            const url = routes.show.replace(':id', id);
            buttons += `
                <a href="${url}" class="btn btn-sm btn-outline-info rounded-pill me-2" title="Ver Detalles">
                    <i class="fas fa-eye"></i>
                </a>`;
        }
        
        if (routes.edit) {
            const url = routes.edit.replace(':id', id);
            buttons += `
                <a href="${url}" class="btn btn-sm btn-outline-secondary rounded-pill me-2" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>`;
        }

        if (routes.destroy) {
            const url = routes.destroy.replace(':id', id);
            buttons += `
                <button type="button" 
                    class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                    data-url="${url}"
                    title="Eliminar">
                    <i class="fas fa-trash"></i>
                </button>`;
        }

        buttons += '</div>';
        return DataGrid.html(buttons);
    }

    /**
     * Inicializa el manejador global de eliminación
     */
    initDeleteHandler() {
        if (window[`hasDeleteHandler_${this.options.containerId}`]) return;
        window[`hasDeleteHandler_${this.options.containerId}`] = true;

        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;

            const url = btn.dataset.url;
            if (!url) return;

            const { title, text } = this.options.deleteMessage;
            
            // Permitir override por data attributes del botón
            const confirmTitle = btn.dataset.title || title;
            const confirmText = btn.dataset.text || text;

            const confirmed = await Notifications.confirm({
                title: confirmTitle,
                text: confirmText,
                confirmButtonText: 'Sí, eliminar/anular',
                confirmButtonColor: '#e74a3b'
            });

            if (confirmed) {
                this.handleDelete(url);
            }
        });
    }

    /**
     * Ejecuta la petición de eliminación
     */
    async handleDelete(url) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.config.tokens.csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ _method: 'DELETE' })
            });

            const result = await response.json();

            if (response.ok) {
                Notifications.success('Éxito', result.message || 'Operación realizada correctamente.');
                
                // Recargar página (o grid si implementamos reload)
                window.location.reload(); 
            } else {
                Notifications.error('Error', result.message || 'No se pudo completar la operación.');
            }
        } catch (error) {
            Notifications.error('Error', 'Ocurrió un error inesperado.');
            console.error(error);
        }
    }
}
