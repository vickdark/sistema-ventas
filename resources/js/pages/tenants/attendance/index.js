import CrudManager from '../../../modules/CrudManager';
import Notifications from '../../../modules/Notifications';
import { getColumns, mapData } from './columns';

export function initAttendanceIndex(config) {
    const elements = {
        btnClockAction: document.getElementById('btnClockAction'),
        currentStatusText: document.getElementById('currentStatusText'),
        filterStartDate: document.getElementById('filterStartDate'),
        filterEndDate: document.getElementById('filterEndDate'),
        btnFilter: document.getElementById('btnFilter'),
    };

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
            const response = await fetch(config.routes.status);
            const data = await response.json();
            
            updateWidget(data);
        } catch (error) {
            console.error('Error loading status:', error);
        }
    }

    function updateWidget(data) {
        if (data.is_clocked_in) {
            // Usuario trabajando
            elements.currentStatusText.innerHTML = `<span class="text-success">EN TURNO</span> <small class="text-muted">desde ${formatTime(data.active_shift.clock_in)}</small>`;
            elements.btnClockAction.innerHTML = `<i class="fas fa-sign-out-alt"></i> MARCAR SALIDA`;
            elements.btnClockAction.classList.remove('btn-primary', 'disabled');
            elements.btnClockAction.classList.add('btn-danger');
            elements.btnClockAction.onclick = () => confirmClockOut(data.active_shift.id);
            elements.btnClockAction.disabled = false;
        } else {
            // Usuario fuera de turno
            const lastShift = data.today_shift;
            const statusText = lastShift 
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
        const confirmed = await Notifications.confirm({
            title: '¿Confirmar Entrada?',
            text: 'Se registrará tu hora de inicio de jornada.',
            icon: 'info',
            confirmButtonText: 'Sí, marcar entrada'
        });

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

        if (notes !== undefined) { 
            const url = config.routes.clock_out.replace(':id', id);
            performClockAction(url, 'PUT', { notes }); 
        }
    }

    async function performClockAction(url, method, body = {}) {
        elements.btnClockAction.disabled = true;
        elements.btnClockAction.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.tokens.csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body)
            });

            const result = await response.json();

            if (response.ok) {
                Notifications.success('Éxito', result.message);
                loadStatus();
                // Refresh grid via global or captured manager
                if (window.attendanceGridManager) {
                    window.attendanceGridManager.grid.forceRender();
                }
            } else {
                Notifications.error('Error', result.message);
                loadStatus(); 
            }
        } catch (error) {
            console.error(error);
            Notifications.error('Error', 'No se pudo conectar con el servidor.');
            loadStatus();
        }
    }

    // Inicializar Widget
    loadStatus();
}
