import DataGrid from '../../../../modules/DataGrid';

export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { 
        id: 'color', 
        name: "Color",
        formatter: (cell) => DataGrid.html(`
            <div class="d-flex align-items-center">
                <div class="rounded-circle shadow-sm" style="width: 20px; height: 20px; background-color: ${cell || '#6c757d'}; border: 1px solid rgba(0,0,0,0.1);"></div>
            </div>
        `)
    },
    { id: 'description', name: "Descripción" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (c) => [
    c.id,
    c.name,
    c.color,
    c.description || '-',
    null
];
