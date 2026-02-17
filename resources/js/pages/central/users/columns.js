import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'email', name: "Email" },
    { 
        id: 'email_verified_at', 
        name: "Verificación Email",
        formatter: (cell) => {
            const isVerified = cell !== null;
            const badgeClass = isVerified ? 'bg-success' : 'bg-warning text-dark';
            const text = isVerified ? 'Verificado' : 'Pendiente';
            return DataGrid.html(`<span class="badge ${badgeClass} rounded-pill">${text}</span>`);
        }
    },
    {
        id: 'resend_verification',
        name: "Reenviar Verificación",
        sort: false,
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const emailVerifiedAt = row.cells[3].data; // Index 3 is email_verified_at
            
            if (emailVerifiedAt === null) {
                return DataGrid.html(`
                    <button type="button"
                        class="btn btn-sm btn-outline-info rounded-pill btn-resend-verification"
                        data-id="${id}"
                        data-url="${routes.resendVerification}"
                        title="Reenviar Correo de Verificación">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                `);
            }
            return '';
        }
    },
    {
        id: 'actions',
        name: "Acciones",
        sort: false,
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const editUrl = routes.edit.replace(':id', id);
            const deleteUrl = routes.destroy.replace(':id', id);
            // Assuming the row object might contain other useful data if mapped correctly,
            // but for now we rely on the ID.

            return DataGrid.html(`
                <div class="btn-group">
                    <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${deleteUrl}"
                        data-title="¿Eliminar Usuario?"
                        data-text="Estás a punto de eliminar al usuario ${id}. Esta acción es IRREVERSIBLE."
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
        }
    }
];

export const mapData = (u) => [
    u.id,
    u.name,
    u.email,
    u.email_verified_at,
    null, // resend_verification column placeholder
    null  // actions column placeholder
];
