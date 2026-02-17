export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'nit_ci', name: "NIT/Documento" },
    { id: 'email', name: "Email" },
    { id: 'phone', name: "Teléfono" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (client) => [
    client.id, 
    client.name, 
    client.nit_ci,
    client.email,
    client.phone,
    null
];
