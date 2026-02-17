import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initProductsIndex(config) {
    new CrudManager(config, {
        columns: columns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar producto?',
            text: 'Esta acción no se puede deshacer.'
        }
    }).init();
}
