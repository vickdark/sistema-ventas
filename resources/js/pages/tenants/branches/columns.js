import DataGrid from '../../../modules/DataGrid';

export const columns = (routes) => [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'address', name: "Dirección" },
    { id: 'phone', name: "Teléfono" },
    { id: 'email', name: "Email" },
    { 
        id: 'is_main', 
        name: "Principal",
        formatter: (cell) => DataGrid.html(cell 
            ? '<span class="badge bg-success rounded-pill">Principal</span>' 
            : '<span class="badge bg-light text-dark rounded-pill">Secundaria</span>')
    },
    { 
        id: 'actions', 
        name: "Acciones",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const editUrl = routes.edit.replace(':id', id);
            const deleteUrl = routes.destroy.replace(':id', id);
            const isMain = row.cells[5].data;

            return DataGrid.html(`
                <div class="btn-group">
                    <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    ${!isMain ? `
                    <button class="btn btn-sm btn-outline-danger rounded-pill btn-delete" data-url="${deleteUrl}" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                    ` : ''}
                </div>
            `);
        }
    }
];

export const mapData = (b) => [
    b.id, 
    b.name, 
    b.address || 'N/A',
    b.phone || 'N/A',
    b.email || 'N/A',
    b.is_main,
    null
];
