import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initExpensesIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Anular registro de gasto?',
            text: 'Esta acción eliminará el registro del gasto permanentemente.'
        }
    });
    manager.init();
}
