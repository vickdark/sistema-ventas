import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initUsersIndex(config) {
    new CrudManager(config, {
        columns: columns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer y el usuario perderá acceso al sistema.'
        }
    }).init();
}
