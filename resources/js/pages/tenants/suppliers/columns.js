export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'company', name: "Empresa" },
    { id: 'phone', name: "Teléfono" },
    { id: 'email', name: "Email" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (supplier) => [
    supplier.id, 
    supplier.name, 
    supplier.company,
    supplier.phone,
    supplier.email,
    null
];
