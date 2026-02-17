import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes, permissions) => [
    { 
        id: 'nombre', 
        name: "Nombre", 
        width: '20%',
        minWidth: '150px'
    },
    { 
        id: 'slug', 
        name: "Identificador",
        width: '15%',
        minWidth: '120px',
        formatter: (cell) => DataGrid.html(`<code class="small text-primary bg-primary-subtle px-2 py-1 rounded">${cell}</code>`)
    },
    { 
        id: 'users_count', 
        name: "Usuarios",
        width: '10%',
        minWidth: '100px',
        formatter: (cell) => DataGrid.html(`
            <div class="text-center">
                <span class="badge bg-light text-dark border rounded-pill px-3">
                    <i class="fas fa-users me-1 text-muted"></i> ${cell}
                </span>
            </div>
        `)
    },
    { 
        id: 'descripcion', 
        name: "Descripción",
        width: '30%',
        formatter: (cell) => DataGrid.html(`<span class="text-muted small">${cell ? (cell.length > 50 ? cell.substring(0, 50) + '...' : cell) : 'Sin descripción'}</span>`)
    },
    { 
        id: 'actions', 
        name: "Acciones", 
        width: '160px',
        sort: false,
        formatter: (cell, row) => {
            const data = cell; 
            const editUrl = routes.edit.replace(':id', data.id);
            const permissionsUrl = routes.permissions.replace(':id', data.id);
            const destroyUrl = routes.destroy.replace(':id', data.id);
            const isSystemRole = data.slug === 'admin' || data.slug === 'vendedor'; 

            let buttons = '<div class="btn-group">';

            if (permissions.canEdit) {
                buttons += `
                    <a href="${permissionsUrl}" class="btn btn-sm btn-outline-info rounded-pill me-1" title="Gestionar Permisos">
                        <i class="fas fa-key"></i>
                    </a>
                    <a href="${editUrl}" class="btn btn-sm btn-outline-secondary rounded-pill me-1" title="Editar Rol">
                        <i class="fas fa-edit"></i>
                    </a>
                `;
            }

            if (permissions.canDestroy && !isSystemRole) {
                buttons += `
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${destroyUrl}"
                        data-title="¿Eliminar Rol?"
                        data-text="Estás a punto de eliminar el rol ${data.nombre}. Esta acción es IRREVERSIBLE."
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
            } else if (isSystemRole) {
                 buttons += `
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" disabled title="Rol de Sistema (Protegido)">
                        <i class="fas fa-lock"></i>
                    </button>
                `;
            }

            buttons += '</div>';
            return DataGrid.html(buttons);
        }
    }
];

export const mapData = (role, routes) => [
    role.nombre,
    role.slug,
    role.users_count,
    role.descripcion,
    { 
        id: role.id, 
        nombre: role.nombre, 
        slug: role.slug 
    }
];
