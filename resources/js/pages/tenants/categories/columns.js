export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (c) => [
    c.id, 
    c.name, 
    null
];
