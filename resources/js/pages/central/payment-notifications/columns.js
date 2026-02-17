import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { 
        id: 'tenant_name', 
        name: "Empresa / Inquilino", 
        width: "25%",
        formatter: (cell, row) => {
            // cell contains tenant_name string based on current controller? 
            // Wait, looking at index.js, cell seems to be an object {name: ..., id: ...} ??
            // Let's check the controller again.
            // Controller returns 'tenant_name' => string, 'tenant_id' => string.
            // But index.js formatter says: cell.name and cell.id. 
            // This implies the mapData in index.js (or controller) was structuring it that way.
            // The controller returns a flat object. 
            // So I need to adjust mapData to create these objects or adjust the formatter to use row.cells.
            // Let's look at the mapData I will implement.
            return DataGrid.html(`
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="fas fa-building small"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">${cell.name}</div>
                        <div class="x-small text-muted">${cell.id}</div>
                    </div>
                </div>
            `);
        }
    },
    { 
        id: 'tenant_contact', 
        name: "Contacto Empresa",
        width: "25%",
        formatter: (cell) => {
            return DataGrid.html(`
                <div class="d-flex flex-column">
                    <div class="small text-dark">
                        <i class="fas fa-envelope me-1 text-muted x-small"></i>${cell.email}
                    </div>
                    <div class="x-small text-muted">
                        <i class="fas fa-phone me-1 text-muted"></i>${cell.phone}
                    </div>
                </div>
            `);
        }
    },
    { 
        id: 'date', 
        name: "Fecha / Hora",
        width: "20%",
        formatter: (cell) => {
            return DataGrid.html(`
                <div class="text-dark small">${cell.date}</div>
                <div class="x-small text-muted">${cell.time}</div>
            `);
        }
    },
    { 
        id: 'status', 
        name: "Estado",
        width: "120px",
        formatter: (cell) => {
            let badgeClass = 'bg-warning';
            let text = 'Pendiente';
            
            if (cell === 'reviewed') {
                badgeClass = 'bg-success';
                text = 'Revisado';
            } else if (cell === 'rejected') {
                badgeClass = 'bg-danger';
                text = 'Rechazado';
            }
            
            return DataGrid.html(`
                <div class="text-center w-100">
                    <span class="badge ${badgeClass} bg-opacity-10 text-${badgeClass.replace('bg-', '')} rounded-pill px-3">${text}</span>
                </div>
            `);
        }
    },
    { 
        id: 'actions',
        name: "Acciones",
        width: "180px",
        formatter: (cell) => {
            const data = cell;
            
            return DataGrid.html(`
                <div class="d-flex justify-content-center gap-2 w-100">
                    <a href="${data.show_url}" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                    </a>

                    ${data.attachment ? `
                        <button type="button" 
                                class="btn btn-sm btn-outline-info rounded-pill d-flex align-items-center justify-content-center btn-preview" 
                                style="width: 32px; height: 32px;"
                                data-attachment="${data.attachment}"
                                data-is-pdf="${data.is_pdf}"
                                title="Previsualizar Comprobante">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </button>
                    ` : ''}
                    
                     <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center justify-content-center btn-delete" 
                        style="width: 32px; height: 32px;"
                        data-url="${data.delete_url}"
                        data-title="¿Eliminar Notificación?"
                        data-text="Estás a punto de eliminar esta notificación. Esta acción es IRREVERSIBLE."
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
        }
    }
];

export const mapData = (n) => [
    { name: n.tenant_name, id: n.tenant_id }, // maps to tenant_name column
    n.tenant_contact, // maps to tenant_contact column
    { date: n.date, time: n.time }, // maps to date column
    n.status, // maps to status column
    { // maps to actions column
        show_url: n.show_url,
        attachment: n.attachment,
        is_pdf: n.is_pdf,
        delete_url: n.delete_url
    }
];
