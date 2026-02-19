import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';
import Notifications from '../../../modules/Notifications';

export function initInventoryIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
    });
    manager.init();

    // Manejador para el botón de Ajuste
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-adjust');
        if (!btn) return;

        const { id, name, stock } = btn.dataset;

        const { value: formValues } = await Swal.fire({
            title: `Ajustar Inventario: ${name}`,
            html: `
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold">Stock Actual: <span class="text-primary">${stock}</span></label>
                </div>
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold">Tipo de Ajuste</label>
                    <select id="adj_type" class="form-select">
                        <option value="input">Entrada (+)</option>
                        <option value="output">Salida (-)</option>
                    </select>
                </div>
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold">Cantidad</label>
                    <input type="number" id="adj_quantity" class="form-control" min="1" value="1">
                </div>
                <div class="text-start mb-3">
                    <label class="form-label small fw-bold">Motivo</label>
                    <select id="adj_reason" class="form-select">
                        <option value="Corrección de Inventario">Corrección de Inventario</option>
                        <option value="Producto Dañado">Producto Dañado</option>
                        <option value="Perdida/Robo">Pérdida/Robo</option>
                        <option value="Donación">Donación</option>
                        <option value="Vencimiento">Vencimiento</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="text-start">
                    <label class="form-label small fw-bold">Descripción (Opcional)</label>
                    <textarea id="adj_description" class="form-control" rows="2"></textarea>
                </div>
            `,
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Procesar Ajuste',
            cancelButtonText: 'Cancelar',
            preConfirm: () => {
                return {
                    product_id: id,
                    type: document.getElementById('adj_type').value,
                    quantity: document.getElementById('adj_quantity').value,
                    reason: document.getElementById('adj_reason').value,
                    description: document.getElementById('adj_description').value
                }
            }
        });

        if (formValues) {
            try {
                window.Notify.loading('Procesando ajuste...');
                const response = await fetch(config.routes.adjust, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formValues)
                });

                const data = await response.json();

                if (data.success) {
                    window.Notify.success('Ajuste procesado', data.message);
                    window.location.reload();
                } else {
                    window.Notify.error('Error', data.message || 'No se pudo realizar el ajuste.');
                }
            } catch (error) {
                console.error(error);
                window.Notify.error('Error', 'Ocurrió un error en la conexión.');
            }
        }
    });
}
