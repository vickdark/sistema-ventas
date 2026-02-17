import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initClientsIndex(config) {
    new CrudManager(config, {
        columns: columns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar cliente?',
            text: 'Esta acción no se puede deshacer.'
        }
    }).init();
}
