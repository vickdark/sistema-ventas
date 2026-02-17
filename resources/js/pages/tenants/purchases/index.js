import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initPurchasesIndex(config) {
    new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar compra?',
            text: 'Esta acción revertirá el stock del producto.'
        }
    }).init();
}
