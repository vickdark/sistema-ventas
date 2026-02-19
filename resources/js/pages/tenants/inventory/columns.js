import DataGrid from '../../../modules/DataGrid';

export const getColumns = (routes) => [
    { id: 'id', name: "ID", width: "60px" },
    { id: 'code', name: "Código", width: "120px" },
    { id: 'name', name: "Producto" },
    { id: 'category', name: "Categoría" },
    { 
        id: 'stock', 
        name: "Stock Actual",
        formatter: (cell, row) => {
            const minStock = row.cells[6].data; // min_stock is 7th column
            const stock = parseInt(cell);
            const color = stock <= minStock ? 'danger' : (stock <= minStock * 1.5 ? 'warning' : 'success');
            return DataGrid.html(`
                <div class="d-flex align-items-center">
                    <span class="badge bg-${color} rounded-pill px-3 me-2">${stock}</span>
                    ${stock <= minStock ? '<i class="fas fa-exclamation-triangle text-danger" title="Bajo Stock"></i>' : ''}
                </div>
            `);
        }
    },
    { 
        id: 'prices', 
        name: "Precios",
        formatter: (cell, row) => {
            const purchase = row.cells[7].data;
            const sale = row.cells[8].data;
            return DataGrid.html(`
                <div class="small">
                    <div class="text-muted">C: $${parseFloat(purchase).toFixed(2)}</div>
                    <div class="fw-bold text-primary">V: $${parseFloat(sale).toFixed(2)}</div>
                </div>
            `);
        }
    },
    { id: 'min_stock', name: "Min", hidden: true },
    { id: 'purchase_price', name: "P_Price", hidden: true },
    { id: 'sale_price', name: "S_Price", hidden: true },
    { 
        id: 'actions',
        name: "Acciones",
        formatter: (cell, row) => {
            const id = row.cells[0].data;
            const kardexUrl = routes.kardex.replace(':id', id);
            
            return DataGrid.html(`
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill btn-adjust me-1" 
                        data-id="${id}" 
                        data-name="${row.cells[2].data}" 
                        data-stock="${row.cells[4].data}"
                        title="Ajustar Stock">
                        <i class="fas fa-sliders-h"></i>
                    </button>
                    <a href="${kardexUrl}" class="btn btn-sm btn-outline-info rounded-pill" title="Ver Kardex">
                        <i class="fas fa-history"></i>
                    </a>
                </div>
            `);
        }
    }
];

export const mapData = (product) => [
    product.id,
    product.code,
    product.name,
    product.category ? product.category.name : 'S/C',
    product.stock,
    null, // prices column
    product.min_stock,
    product.purchase_price,
    product.sale_price,
    null // actions
];
