import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "ID", width: "60px" },
    { id: 'number', name: "Número", width: "120px" },
    { id: 'sale', name: "Venta Ref." },
    { id: 'reason', name: "Motivo" },
    { id: 'total', name: "Importe" },
    { 
        id: 'status', 
        name: "Estado",
        formatter: (cell) => {
            const status = cell.toLowerCase();
            const color = status === 'active' ? 'success' : 'danger';
            const text = status === 'active' ? 'Activa' : 'Anulada';
            return DataGrid.html(`<span class="badge bg-${color} rounded-pill">${text}</span>`);
        }
    },
    { id: 'created_at', name: "Fecha" },
    { 
        id: 'actions',
        name: "Acciones",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const status = row.cells[5].data; // raw string from mapData ('active' or 'void')
            const showUrl = routes.show.replace(':id', id);
            const deleteUrl = routes.destroy.replace(':id', id);
            
            let deleteBtn = '';
            if (status === 'active') {
                 deleteBtn = `
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${deleteUrl}"
                        title="Anular Nota">
                        <i class="fas fa-ban"></i>
                    </button>
                `;
            }

            return DataGrid.html(`
                <div class="btn-group">
                    <a href="${showUrl}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver Detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    ${deleteBtn}
                </div>
            `);
        }
    }
];

export const mapData = (note) => [
    note.id || '', 
    note.number || '',
    `Venta #${note.sale ? note.sale.nro_venta : 'N/A'}`,
    note.reason || '',
    `$${parseFloat(note.total || 0).toLocaleString()}`,
    note.status || 'active',
    note.created_at ? new Date(note.created_at).toLocaleDateString() : '',
    null
];
