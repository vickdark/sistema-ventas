import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "ID", width: "60px" },
    { id: 'nro_venta', name: "Nro. Venta", width: "100px" },
    { id: 'client', name: "Cliente" },
    { id: 'total_paid', name: "Total" },
    { 
        id: 'payment_type', 
        name: "Tipo Pago",
        formatter: (cell) => {
            const type = cell.toLowerCase();
            const color = type === 'credito' ? 'warning' : 'info';
            return DataGrid.html(`<span class="badge bg-${color} rounded-pill">${cell}</span>`);
        }
    },
    { 
        id: 'payment_status', 
        name: "Estado",
        formatter: (cell) => {
            const status = cell.toLowerCase();
            const color = status === 'pagado' ? 'success' : 'danger';
            return DataGrid.html(`<span class="badge bg-${color} rounded-pill">${cell}</span>`);
        }
    },
    { id: 'sale_date', name: "Fecha" },
    { 
        id: 'actions',
        name: "Acciones",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const showUrl = routes.show.replace(':id', id);
            const ticketUrl = routes.ticket.replace(':id', id);
            const deleteUrl = routes.destroy.replace(':id', id);
            
            return DataGrid.html(`
                <div class="btn-group">
                    <a href="${showUrl}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver Detalles">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="${ticketUrl}" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill mx-1" title="Imprimir Ticket">
                        <i class="fas fa-print"></i>
                    </a>
                    <button type="button" 
                        class="btn btn-sm btn-outline-danger rounded-pill btn-delete" 
                        data-url="${deleteUrl}"
                        title="Anular Venta">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `);
        }
    }
];

export const mapData = (sale) => [
    sale.id, 
    sale.nro_venta,
    sale.client ? sale.client.name : 'Venta Rápida', 
    `$${parseFloat(sale.total_paid).toLocaleString()}`,
    sale.payment_type,
    sale.payment_status,
    sale.sale_date,
    null
];
