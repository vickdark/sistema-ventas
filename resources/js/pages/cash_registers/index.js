import DataGrid from '../../modules/DataGrid';
import Notifications from '../../modules/Notifications';

export function initCashRegistersIndex(config) {
    const { routes } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "60px" },
            { id: 'name', name: "Caja" },
            { id: 'opening_date', name: "Apertura" },
            { id: 'closing_date', name: "Cierre" },
            { id: 'initial_amount', name: "Monto Inicial" },
            { id: 'final_amount', name: "Monto Final" },
            { id: 'user', name: "Usuario" },
            { 
                id: 'status', 
                name: "Estado",
                formatter: (cell) => {
                    const active = cell === 'abierta';
                    return DataGrid.html(`<span class="badge rounded-pill ${active ? 'bg-success' : 'bg-secondary'}">${cell.toUpperCase()}</span>`);
                }
            },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const showUrl = routes.show.replace(':id', id);
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            <a href="${showUrl}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    `);
                }
            }
        ],
        mapData: (register) => [
            register.id, 
            register.name || 'N/A',
            register.opening_date,
            register.closing_date || 'N/A',
            `$${parseFloat(register.initial_amount).toFixed(2)}`,
            register.final_amount ? `$${parseFloat(register.final_amount).toFixed(2)}` : 'N/A',
            register.user ? register.user.name : 'N/A',
            register.status,
            null
        ]
    }).render();
}
