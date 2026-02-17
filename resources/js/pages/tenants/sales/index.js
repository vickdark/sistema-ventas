import DataGrid from '../../../modules/DataGrid';
import Notifications from '../../../modules/Notifications';

export function initSalesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
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
                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                onclick="window.deleteSale('${deleteUrl}')"
                                title="Anular Venta">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (sale) => [
            sale.id, 
            sale.nro_venta,
            sale.client ? sale.client.name : 'Venta Rápida', 
            `$${parseFloat(sale.total_paid).toLocaleString()}`,
            sale.payment_type,
            sale.payment_status,
            sale.sale_date,
            null
        ]
    }).render();

    window.deleteSale = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Anular venta?',
            text: 'Se restaurará el stock de los productos vendidos.',
            confirmButtonText: 'Sí, anular',
            confirmButtonColor: '#e74a3b'
        });

        if (confirmed) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                if (response.ok) {
                    Notify.success('Anulada', 'La venta ha sido anulada correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', 'No se pudo anular la venta.');
                }
            } catch (error) {
                Notify.error('Error', 'Error de conexión.');
                console.error(error);
            }
        }
    };
}
