export function initProductsEdit() {
    new TomSelect('#supplier_ids', {
        plugins: ['remove_button'],
        create: false,
        placeholder: 'Selecciona uno o más proveedores...',
        render: {
            option: function(data, escape) {
                return '<div><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
            },
            item: function(data, escape) {
                return '<div title="' + escape(data.text) + '"><i class="fas fa-truck me-2 opacity-50"></i>' + escape(data.text) + '</div>';
            }
        }
    });

    // Inicializar TomSelect para categoría
    const categorySelect = document.getElementById('category_id');
    if (categorySelect) {
        new TomSelect(categorySelect, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: 'Selecciona una categoría',
            render: {
                option: function(data, escape) {
                    return '<div><i class="fas fa-tag me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                },
                item: function(data, escape) {
                    return '<div title="' + escape(data.text) + '"><i class="fas fa-tag me-2 opacity-50"></i>' + escape(data.text) + '</div>';
                }
            }
        });
    }
}
