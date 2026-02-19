import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initCreditNotesIndex(config) {
    console.log('initCreditNotesIndex: Starting with config', config);
    try {
        const manager = new CrudManager(config, {
            columns: getColumns(config.routes),
            mapData: mapData,
            deleteMessage: {
                title: '¿Anular nota de crédito?',
                text: 'Se revertirá el ingreso de stock y la nota quedará como anulada.'
            }
        });
        manager.init();
        console.log('initCreditNotesIndex: Manager initialized');
    } catch (error) {
        console.error('initCreditNotesIndex: Error during initialization', error);
    }
}
