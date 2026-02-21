import CrudManager from '../../../modules/CrudManager';
import Notifications from '../../../modules/Notifications';
import { getColumns, mapData } from './columns';

export function initAttendanceIndex(config) {
    const elements = {
        btnClockAction: document.getElementById('btnClockAction'),
        currentStatusText: document.getElementById('currentStatusText'),
        filterUser: document.getElementById('filterUser'),
        filterStartDate: document.getElementById('filterStartDate'),
        filterEndDate: document.getElementById('filterEndDate'),
        btnFilter: document.getElementById('btnFilter'),
    };

    // Initialize TomSelect for Employee Filter
    if (elements.filterUser && window.TomSelect) {
        new TomSelect(elements.filterUser, {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: 'Buscar empleado...',
            plugins: ['dropdown_input'],
            allowEmptyOption: true,
        });
    }

    // 1. Grid Logic
    new CrudManager(config, {
        containerId: 'attendanceGrid',
        columns: getColumns(config),
        mapData: mapData,
        gridOptions: {
            pagination: {
                limit: 10,
                server: {
                    url: (prev, page, limit) => {
                        let url = `${prev}?limit=${limit}&offset=${page * limit}`;
                        if (elements.filterUser && elements.filterUser.value) url += `&user_id=${elements.filterUser.value}`;
                        if (elements.filterStartDate) url += `&start_date=${elements.filterStartDate.value}`;
                        if (elements.filterEndDate) url += `&end_date=${elements.filterEndDate.value}`;
                        return url;
                    }
                }
            }
        },
        onInit: (manager) => {
            if (elements.btnFilter) {
                elements.btnFilter.onclick = () => {
                    manager.grid.forceRender();
                };
            }
            // Expose manager for external use if needed, or internal refresh
            // manager.grid can be used to refresh after clock actions
            window.attendanceGridManager = manager; 
        }
    }).init();

    // 2. Widget Logic
    // Cargar Estado Actual
    async function loadStatus() {
        try {
            const response = await axios.get(config.routes.status);
            console.log('Status Response:', response.data);
            updateWidget(response.data);
        } catch (error) {
            console.error('Error loading status:', error);
        }
    }

    function updateWidget(data) {
        if (!elements.currentStatusText || !elements.btnClockAction) return;

        // Asegurarnos de que removemos listeners anteriores
        const newBtn = elements.btnClockAction.cloneNode(true);
        elements.btnClockAction.parentNode.replaceChild(newBtn, elements.btnClockAction);
        elements.btnClockAction = newBtn;

        if (data.is_clocked_in && data.active_shift) {
            // Usuario trabajando
            elements.currentStatusText.innerHTML = `<span class="text-success">EN TURNO</span> <small class="text-muted">desde ${formatTime(data.active_shift.clock_in)}</small>`;
            elements.btnClockAction.innerHTML = `<i class="fas fa-sign-out-alt"></i> MARCAR SALIDA`;
            elements.btnClockAction.classList.remove('btn-primary', 'disabled');
            elements.btnClockAction.classList.add('btn-danger');
            
            const shiftId = data.active_shift.id;
            elements.btnClockAction.onclick = () => confirmClockOut(shiftId);
            elements.btnClockAction.disabled = false;
        } else {
            // Usuario fuera de turno
            const lastShift = data.today_shift;
            const statusText = lastShift && lastShift.clock_out
                ? `<span class="text-muted">Última salida: ${formatTime(lastShift.clock_out)}</span>` 
                : '<span class="text-muted">Sin actividad hoy</span>';

            elements.currentStatusText.innerHTML = statusText;
            elements.btnClockAction.innerHTML = `<i class="fas fa-sign-in-alt"></i> MARCAR ENTRADA`;
            elements.btnClockAction.classList.remove('btn-danger', 'disabled');
            elements.btnClockAction.classList.add('btn-primary');
            
            elements.btnClockAction.onclick = () => confirmClockIn();
            elements.btnClockAction.disabled = false;
        }
    }

    function formatTime(isoString) {
        if (!isoString) return '--:--';
        return new Date(isoString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Acciones (Entrada/Salida)
    async function confirmClockIn() {
        const confirmed = await Notifications.confirm('¿Confirmar Entrada?', 'Se registrará tu hora de inicio de jornada.');

        if (confirmed) {
            performClockAction(config.routes.clock_in, 'POST');
        }
    }

    async function confirmClockOut(id) {
        const { value: notes } = await Swal.fire({
            title: '¿Confirmar Salida?',
            text: 'Puedes añadir una nota opcional (ej: Almuerzo, Fin de jornada).',
            input: 'textarea',
            inputPlaceholder: 'Notas...',
            showCancelButton: true,
            confirmButtonText: 'Marcar Salida',
            cancelButtonText: 'Cancelar'
        });

        // Si el usuario no cancela (value no es undefined)
        if (notes !== undefined) { 
             // Reemplazar FAKE_ID en la ruta
            let url = config.routes.clock_out.replace('FAKE_ID', id);
            
            // Enviamos clock_out: true explicitamente
            performClockAction(url, 'PUT', { 
                notes: notes,
                clock_out: true,
                _method: 'PUT' // A veces Laravel necesita esto si axios se comporta raro, aunque PUT deberia funcionar
            }); 
        }
    }

    async function performClockAction(url, method, body = {}) {
        elements.btnClockAction.disabled = true;
        elements.btnClockAction.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

        try {
            const config = {
                method: method.toLowerCase(),
                url: url,
                data: body
            };

            const response = await axios(config);

            if (response.data) {
                Notifications.success('Éxito', response.data.message);
                loadStatus();
                if (window.attendanceGridManager) {
                    window.attendanceGridManager.grid.forceRender();
                }
            }
        } catch (error) {
            console.error('Clock Action Error:', error);
            const message = error.response?.data?.message || 'No se pudo conectar con el servidor.';
            Notifications.error('Error', message);
            loadStatus();
        }
    }

    // Inicializar Widget
    loadStatus();
}
