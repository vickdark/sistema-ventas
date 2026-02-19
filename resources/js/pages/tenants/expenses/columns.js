export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'date', name: "Fecha" },
    { id: 'name', name: "Concepto" },
    { id: 'category', name: "Categoría" },
    { id: 'amount', name: "Monto" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (e) => [
    e.id,
    e.date || '-',
    e.name,
    e.category ? e.category.name : '-',
    e.amount,
    null
];
