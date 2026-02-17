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
}
