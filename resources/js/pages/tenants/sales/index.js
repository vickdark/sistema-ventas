import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

import '../../../../css/pages/tenants/sales/sales.css';

export function initSalesIndex(config) {
    new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Anular venta?',
            text: 'Se restaurará el stock de los productos vendidos.'
        }
    }).init();
}
