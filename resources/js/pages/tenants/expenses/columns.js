import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'date', name: "Fecha", width: "120px" },
    { id: 'name', name: "Concepto" },
    { id: 'category', name: "Categoría" },
    { id: 'amount', name: "Monto", width: "120px" },
    { 
        id: 'actions', 
        name: "Acciones", 
        width: "120px",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const editUrl = routes.edit.replace(':id', id);
            const deleteUrl = routes.destroy.replace(':id', id);

            return DataGrid.html(`
                <div class="btn-group">
                    <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${deleteUrl}"
                        title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
        }
    }
];

export const mapData = (e) => [
    e.id,
    e.date, // Ya viene formateado del controlador como dd/mm/yyyy
    e.name,
    DataGrid.html(`
        <div class="d-flex align-items-center">
            <span class="rounded-circle me-2" style="width: 10px; height: 10px; background-color: ${e.category_color}"></span>
            <span>${e.category_name}</span>
        </div>
    `),
    DataGrid.html(`<span class="fw-bold text-dark">$${parseFloat(e.amount).toFixed(2)}</span>`),
    null
];
