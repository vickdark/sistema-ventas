import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initBranchesIndex(config) {
    new CrudManager(config, {
        columns: columns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar sucursal?',
            text: 'Esta acción no se puede deshacer. Se perderá el historial asociado.'
        }
    }).init();
}
