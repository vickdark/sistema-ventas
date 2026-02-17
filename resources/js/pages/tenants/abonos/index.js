import DataGrid from '../../../modules/DataGrid';
import Notifications from '../../../modules/Notifications';

export function initAbonosIndex(config) {
    const { routes, tokens } = config;

    // 1. Grid de Clientes con Deuda (Deudores)
    const debtorsGrid = new DataGrid("debtorsWrapper", {
        url: routes.index + "?type=debtors",
        columns: [
            { id: 'id', name: "ID", width: "60px" },
            { id: 'name', name: "Cliente" },
            { id: 'nit_ci', name: "NIT/CI" },
            { 
                id: 'sales_count', 
                name: "Facturas Pendientes",
                formatter: (cell) => DataGrid.html(`<span class="badge bg-danger rounded-pill">${cell} pendientes</span>`)
            },
            { 
                id: 'total_debt', 
                name: "Saldo Total",
                formatter: (cell) => DataGrid.html(`<span class="fw-bold text-primary">$${parseFloat(cell).toLocaleString()}</span>`)
            },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const clientId = row.cells[0].data;
                    const createUrl = `${routes.create}?client_id=${clientId}`;
                    
                    return DataGrid.html(`
                        <a href="${createUrl}" class="btn btn-sm btn-primary rounded-pill px-3">
                            <i class="fas fa-hand-holding-dollar me-1"></i> Gestionar Abono
                        </a>
                    `);
                }
            }
        ],
        mapData: (client) => [
            client.id, 
            client.name,
            client.nit_ci,
            client.sales_count,
            client.total_debt,
            null
        ]
    }).render();

    // 2. Grid de Historial de Abonos
    const historyGrid = new DataGrid("historyWrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "60px" },
            { id: 'client', name: "Cliente" },
            { id: 'sale', name: "Nro. Venta" },
            { 
                id: 'payment_type', 
                name: "Tipo Pago",
                formatter: (cell) => {
                    const type = cell || 'CONTADO';
                    const badgeClass = type === 'CONTADO' ? 'bg-success' : 'bg-primary';
                    const badgeText = type === 'CONTADO' ? 'Efectivo' : 'Transferencia';
                    return DataGrid.html(`<span class="badge ${badgeClass} rounded-pill">${badgeText}</span>`);
                }
            },
            { id: 'amount', name: "Monto" },
            { id: 'created_at', name: "Fecha" },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const deleteUrl = routes.destroy.replace(':id', id);
                    
                    return DataGrid.html(`
                        <button type="button" 
                            class="btn btn-sm btn-outline-danger rounded-pill" 
                            onclick="window.deleteAbono('${deleteUrl}')"
                            title="Eliminar Abono">
                            <i class="fas fa-trash"></i>
                        </button>
                    `);
                }
            }
        ],
        mapData: (abono) => [
            abono.id, 
            abono.client ? abono.client.name : 'N/A',
            abono.sale ? `#${abono.sale.nro_venta}` : 'PAGO GENERAL',
            abono.payment_type,
            `$${parseFloat(abono.amount).toLocaleString()}`,
            new Date(abono.created_at).toLocaleDateString(),
            null
        ]
    }).render();

    window.deleteAbono = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar abono?',
            text: 'Esto restaurará la deuda de la venta.',
            confirmButtonText: 'Sí, eliminar',
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
                    Notify.success('Eliminado', 'El abono ha sido eliminado.');
                    historyGrid.forceRender(); // Solo recargamos el historial
                    debtorsGrid.forceRender(); // Y actualizamos el saldo de deudores
                } else {
                    Notify.error('Error', 'No se pudo eliminar el abono.');
                }
            } catch (error) {
                Notify.error('Error de conexión.');
                console.error(error);
            }
        }
    };
}
