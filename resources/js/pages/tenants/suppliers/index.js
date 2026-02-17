import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initSuppliersIndex(config) {
    new CrudManager(config, {
        columns: columns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar proveedor?',
            text: 'Esta acción no se puede deshacer.'
        }
    }).init();
}
