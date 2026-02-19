export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'color', name: "Color" },
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
