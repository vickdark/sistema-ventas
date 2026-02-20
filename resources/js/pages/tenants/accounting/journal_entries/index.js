import CrudManager from '../../../../modules/CrudManager';
import { columns, mapData } from './columns';

export function initJournalEntriesIndex(config) {
    const elements = {
        filterStartDate: document.getElementById('filterStartDate'),
        filterEndDate: document.getElementById('filterEndDate'),
        btnFilter: document.getElementById('btnFilter'),
    };

    new CrudManager(config, {
        containerId: 'journalEntriesGrid',
        columns: columns,
        mapData: mapData,
        gridOptions: {
            pagination: {
                limit: 10,
                server: {
                    url: (prev, page, limit) => {
                        let url = `${prev}?limit=${limit}&offset=${page * limit}`;
                        if (elements.filterStartDate) url += `&start_date=${elements.filterStartDate.value}`;
                        if (elements.filterEndDate) url += `&end_date=${elements.filterEndDate.value}`;
                        return url;
                    }
                }
            }
        },
        onInit: (manager) => {
            if (elements.btnFilter) {
                elements.btnFilter.onclick = () => {
                    manager.grid.forceRender();
                };
            }
        }
    }).init();
}
