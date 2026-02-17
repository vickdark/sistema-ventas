import DataGrid from '../../../modules/DataGrid';
import CrudManager from '../../../modules/CrudManager';
import { historyColumns, mapHistory, debtorsColumns, mapDebtors } from './columns';

export function initAbonosIndex(config) {
    const { routes } = config;

    // 1. Grid de Deudores (Personalizado)
    // Definimos la columna de acciones aquí para tener acceso a `routes`
    const finalDebtorsColumns = [...debtorsColumns];
    finalDebtorsColumns[5] = { // Índice de acciones
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
    };

    new DataGrid("debtorsWrapper", {
        url: routes.index + "?type=debtors",
        columns: finalDebtorsColumns,
        mapData: mapDebtors
    }).render();

    // 2. Grid de Historial (Usando CrudManager para manejar eliminación)
    new CrudManager(config, {
        containerId: "historyWrapper",
        columns: historyColumns,
        mapData: mapHistory,
        deleteMessage: {
            title: '¿Eliminar abono?',
            text: 'Esto restaurará la deuda de la venta.'
        }
    }).init();
}
