import CrudManager from '../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initActivityLogsIndex(config) {
    new CrudManager(config, {
        columns: columns,
        mapData: mapData
    }).init();
}
