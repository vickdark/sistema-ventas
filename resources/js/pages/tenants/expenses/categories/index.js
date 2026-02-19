import CrudManager from '../../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initExpenseCategoriesIndex(config) {
    const manager = new CrudManager(config, {
        columns: columns,
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar categoría?',
            text: 'Esta acción no se puede deshacer. Los gastos asociados a esta categoría podrían verse afectados.'
        }
    });
    manager.init();
}
