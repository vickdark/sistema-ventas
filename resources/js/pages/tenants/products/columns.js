import DataGrid from '../../../modules/DataGrid';

export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { 
        id: 'image', 
        name: "Imagen",
        formatter: (cell) => {
            if (!cell) return DataGrid.html('<div class="text-center"><i class="fas fa-image text-light"></i></div>');
            return DataGrid.html(`<img src="/storage/${cell}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">`);
        }
    },
    { id: 'code', name: "Código" },
    { id: 'name', name: "Nombre" },
    { id: 'sale_price', name: "Precio Venta" },
    { id: 'stock', name: "Stock" },
    { id: 'category', name: "Categoría" },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (product) => [
    product.id, 
    product.image,
    product.code, 
    product.name,
    `$${product.sale_price}`,
    product.stock,
    product.category ? product.category.name : 'Sin Categoría',
    null
];
