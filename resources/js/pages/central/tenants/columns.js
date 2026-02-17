import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "Empresa / ID", width: "200px" },
    { 
        id: 'domain', 
        name: "Dominio de Acceso",
        formatter: (cell) => DataGrid.html(`<a href="http://${cell}" target="_blank" class="text-primary text-decoration-none fw-bold"><i class="fas fa-external-link-alt me-1 small"></i>${cell}</a>`)
    },
    { 
        id: 'database', 
        name: "Base de Datos",
        formatter: (cell) => DataGrid.html(`<span class="badge bg-light text-dark border"><i class="fas fa-database me-1 small opacity-50"></i>${cell}</span>`)
    },
    { 
        id: 'status', 
        name: "Estado de Cuenta",
        formatter: (cell, row) => {
            const isPaid = cell === true || cell === 1 || cell === '1';
            
            let badgeClass = isPaid ? 'bg-success' : 'bg-danger';
            let text = isPaid ? 'ACTIVO' : 'SUSPENDIDO';
            let icon = isPaid ? 'fa-check-circle' : 'fa-ban';
            
            return DataGrid.html(`
                <span class="badge ${badgeClass} rounded-pill px-3 py-2 shadow-sm" style="font-size: 0.75rem;">
                    <i class="fas ${icon} me-1"></i>${text}
                </span>
            `);
        }
    },
    { 
        id: 'actions',
        name: "Acciones",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const tenantData = cell; 
            const isPaid = tenantData.is_paid === true || tenantData.is_paid === 1 || tenantData.is_paid === '1';
            
            if (!tenantData || typeof tenantData !== 'object') {
                return '';
            }

            const editUrl = routes.edit.replace(':id', id);
            const destroyUrl = routes.destroy.replace(':id', id);
            const markPaidUrl = routes.markPaid.replace(':id', id);
            const suspendUrl = routes.index + `/${id}/suspend`; // Asumiendo ruta estándar si no está en config

            // Escapar JSON para atributo HTML
            const tenantJson = JSON.stringify(tenantData).replace(/"/g, "&quot;");
            
            return DataGrid.html(`
                <div class="btn-group">
                    ${!isPaid ? `
                        <button type="button" 
                            class="btn btn-sm btn-outline-success rounded-pill me-2 btn-mark-paid" 
                            data-url="${markPaidUrl}"
                            data-id="${id}"
                            title="Activar (Marcar Pagado)">
                            <i class="fas fa-check"></i>
                        </button>
                    ` : `
                        <button type="button" 
                            class="btn btn-sm btn-outline-danger rounded-pill me-2 btn-suspend" 
                            data-url="${suspendUrl}"
                            data-id="${id}"
                            title="Suspender Servicio">
                            <i class="fas fa-ban"></i>
                        </button>
                    `}
                    <button type="button" 
                        class="btn btn-sm btn-outline-info rounded-pill me-2 btn-details" 
                        data-tenant='${tenantJson}'
                        title="Ver Detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar Empresa">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${destroyUrl}"
                        data-title="¿Eliminar Empresa?"
                        data-text="Estás a punto de eliminar la empresa ${id}. Esta acción es IRREVERSIBLE."
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
        }
    }
];

export const mapData = (t, config) => [
    t.id, 
    t.domains && t.domains.length > 0 ? t.domains[0].domain : 'N/A',
    t.tenancy_db_name || `${config.db_prefix}_${t.id}`,
    t.is_paid,
    t // Enviamos el objeto completo a la columna 'actions'
];
