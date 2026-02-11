import DataGrid from '../../modules/DataGrid';
import Notifications from '../../modules/Notifications';

export function initProductsIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
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
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const showUrl = routes.show.replace(':id', id);
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            <a href="${showUrl}" class="btn btn-sm btn-outline-info rounded-pill me-2" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary rounded-pill me-2" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                onclick="window.deleteProduct('${deleteUrl}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (product) => [
            product.id, 
            product.image,
            product.code, 
            product.name,
            `$${product.sale_price}`,
            product.stock,
            product.category ? product.category.name : 'Sin Categoría',
            null
        ]
    }).render();

    window.deleteProduct = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar producto?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#e74a3b'
        });

        if (confirmed) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Eliminado', 'El producto ha sido eliminado correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar el producto.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
