import DataGrid from "../../../modules/DataGrid";

export const columns = [
    { id: 'id', name: 'ID', hidden: true },
    { id: 'user', name: 'Usuario' },
    { 
        id: 'action', 
        name: 'Acción',
        formatter: (cell) => DataGrid.html(cell)
    },
    { id: 'model', name: 'Módulo' },
    { id: 'description', name: 'Descripción' },
    { id: 'date', name: 'Fecha' },
    { id: 'actions', name: 'Acciones' }
];

export const mapData = (log) => [
    log.id,
    log.user,
    log.action,
    log.model,
    log.description,
    log.date,
    null
];
