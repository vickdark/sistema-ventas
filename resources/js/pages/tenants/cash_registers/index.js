import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initCashRegistersIndex(config) {
    new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData
        // No necesitamos deleteMessage porque no hay botón de borrar
    }).init();
}
