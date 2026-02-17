import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initRolesIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes, config.permissions),
        mapData: (role) => mapData(role, config.routes),
        deleteMessage: {
            title: '¿Eliminar Rol?',
            text: 'Esta acción es IRREVERSIBLE y podría afectar a los usuarios asignados.'
        }
    });

    manager.init();
}
