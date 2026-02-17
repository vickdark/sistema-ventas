import CrudManager from '../../../modules/CrudManager';
import Notifications from '../../../modules/Notifications';
import { getColumns, mapData } from './columns';

export function initTenantsIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: (t) => mapData(t, config),
        deleteMessage: {
            title: '¿Eliminar Empresa?',
            text: 'Esta acción es IRREVERSIBLE y eliminará todos los datos asociados.'
        }
    });

    manager.init();

    // Handlers personalizados
    initCustomHandlers(config.tokens.csrf);
}

function initCustomHandlers(csrfToken) {
    const container = document.getElementById('wrapper'); // CrudManager usa 'wrapper' por defecto
    if (!container) return;

    // Delegación de eventos
    container.addEventListener('click', async (e) => {
        const btnMarkPaid = e.target.closest('.btn-mark-paid');
        const btnSuspend = e.target.closest('.btn-suspend');
        const btnDetails = e.target.closest('.btn-details');

        if (btnMarkPaid) {
            handleMarkPaid(btnMarkPaid, csrfToken);
        } else if (btnSuspend) {
            handleSuspend(btnSuspend, csrfToken);
        } else if (btnDetails) {
            const tenantData = JSON.parse(btnDetails.dataset.tenant);
            showTenantDetails(tenantData);
        }
    });
}

async function handleMarkPaid(btn, csrfToken) {
    const url = btn.dataset.url;
    const id = btn.dataset.id;

    const confirmed = await Notifications.confirm({
        title: '¿Confirmar Pago?',
        text: `¿Estás seguro de marcar la empresa "${id}" como PAGADA? Esto extenderá su fecha de vencimiento.`,
        confirmButtonText: 'Sí, confirmar pago',
        confirmButtonColor: '#1cc88a'
    });

    if (confirmed) {
        try {
            Notifications.loading('Actualizando estado...');
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                await Notifications.success(result.message, '¡Pago Confirmado!');
                window.location.reload();
            } else {
                Notifications.error('Error', result.message || 'No se pudo actualizar el pago.');
            }
        } catch (error) {
            Notifications.error('Error', 'Ocurrió un error inesperado.');
            console.error(error);
        }
    }
}

async function handleSuspend(btn, csrfToken) {
    const url = btn.dataset.url;
    const id = btn.dataset.id;

    const confirmed = await Notifications.confirm({
        title: '¿Suspender Servicio?',
        text: `¿Estás seguro de SUSPENDER la empresa "${id}"? El acceso será bloqueado.`,
        confirmButtonText: 'Sí, suspender',
        confirmButtonColor: '#e74a3b'
    });

    if (confirmed) {
        try {
            Notifications.loading('Suspendiendo empresa...');
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (response.ok) {
                await Notifications.success(result.message, 'Empresa Suspendida');
                window.location.reload();
            } else {
                Notifications.error('Error', result.message || 'No se pudo suspender la empresa.');
            }
        } catch (error) {
            Notifications.error('Error', 'Ocurrió un error inesperado.');
            console.error(error);
        }
    }
}

function showTenantDetails(tenant) {
    if (!tenant) return;
    
    const d = tenant.data || {};
    
    const getVal = (key, fallback = 'N/A') => {
        if (tenant[key] !== undefined && tenant[key] !== null) return tenant[key];
        if (d[key] !== undefined && d[key] !== null) return d[key];
        return fallback;
    };
    
    const isPaidVal = getVal('is_paid', true);
    const isPaid = isPaidVal === true || isPaidVal === 1 || isPaidVal === '1';
                   
    const rawServiceType = getVal('service_type', 'subscription');
    const period = getVal('subscription_period', '30');
    
    let serviceTypeDisplay = '';
    let periodDisplay = '';

    if (rawServiceType === 'subscription') {
        serviceTypeDisplay = 'Suscripción';
        if (period == '30') periodDisplay = 'Mensual (30 días)';
        else if (period == '90') periodDisplay = 'Trimestral (90 días)';
        else if (period == '365') periodDisplay = 'Anual (365 días)';
        else periodDisplay = `${period} días`;
    } else {
        serviceTypeDisplay = 'Compra / Mantenimiento';
        periodDisplay = 'Anual (Mantenimiento)';
    }
    
    const statusBadge = isPaid 
        ? '<span class="badge bg-success rounded-pill px-3">ACTIVO</span>' 
        : '<span class="badge bg-danger rounded-pill px-3">SUSPENDIDO</span>';

    const businessName = getVal('business_name', tenant.id || 'N/A');
    const legalName = getVal('legal_name');
    const taxId = getVal('tax_id');
    const email = getVal('email');
    const phone = getVal('phone');
    const address = getVal('address', 'No especificada');
    const logo = getVal('logo', null);
    const currency = getVal('currency', 'COP');
    const businessType = getVal('business_type');
    const timezone = getVal('timezone');
    const website = getVal('website');
    const createdAt = getVal('created_at');
    const updatedAt = getVal('updated_at');
    const invoiceFooter = getVal('invoice_footer', null);
    const nextPaymentDate = getVal('next_payment_date');
    const dbName = tenant.tenancy_db_name || tenant.db_name || d.db_name || 'N/A';

    const content = `
        <div class="text-start" style="font-size: 0.9rem;">
            <div class="row g-3">
                <div class="col-12 text-center mb-3">
                    ${logo ? `<img src="/storage/${logo}" class="rounded-3 shadow-sm mb-3" style="max-height: 80px; width: auto; object-fit: contain;">` : '<div class="bg-light p-3 rounded-circle d-inline-block shadow-sm"><i class="fas fa-building fa-2x text-muted"></i></div>'}
                    <h4 class="fw-bold mb-0 mt-2 text-dark">${businessName}</h4>
                    <p class="text-muted small mb-1">${tenant.id}.${window.location.host}</p>
                    <div class="mb-2">${statusBadge}</div>
                    ${website !== 'N/A' ? `<a href="${website.startsWith('http') ? website : 'https://' + website}" target="_blank" class="text-primary small text-decoration-none"><i class="fas fa-globe me-1"></i>${website}</a>` : ''}
                </div>
                
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Nombre Legal</label>
                    <span class="fw-bold text-dark">${legalName}</span>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">NIT / RUC</label>
                    <span class="fw-bold text-dark">${taxId}</span>
                </div>

                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Tipo de Servicio</label>
                    <span class="fw-bold text-primary">${serviceTypeDisplay}</span>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Periodo de Renovación</label>
                    <span class="fw-bold text-info">${periodDisplay}</span>
                </div>

                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Próximo Vencimiento</label>
                    <span class="fw-bold ${!isPaid ? 'text-danger' : 'text-success'}">${nextPaymentDate}</span>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Email de Contacto</label>
                    <span class="fw-bold text-dark">${email}</span>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Teléfono</label>
                    <span class="fw-bold text-dark">${phone}</span>
                </div>

                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Moneda / Zona Horaria</label>
                    <span class="fw-bold text-dark">${currency} / ${timezone}</span>
                </div>
                <div class="col-md-6 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Giro de Negocio</label>
                    <span class="fw-bold text-dark">${businessType}</span>
                </div>

                <div class="col-md-12 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Base de Datos</label>
                    <span class="badge bg-light text-dark border font-monospace w-100 text-start">${dbName}</span>
                </div>

                <div class="col-12 border-bottom pb-2">
                    <label class="text-muted small d-block mb-0 font-weight-bold">Dirección Física</label>
                    <span class="fw-bold text-dark">${address}</span>
                </div>

                <div class="col-md-6 pt-1">
                    <label class="text-muted small d-block mb-0">Creado el:</label>
                    <span class="small text-muted">${createdAt}</span>
                </div>
                <div class="col-md-6 pt-1 text-md-end">
                    <label class="text-muted small d-block mb-0">Actualizado el:</label>
                    <span class="small text-muted">${updatedAt}</span>
                </div>

                ${invoiceFooter ? `
                <div class="col-12 bg-light p-2 rounded border-start border-primary border-4 mt-3">
                    <label class="text-muted small d-block mb-0">Pie de Factura:</label>
                    <p class="mb-0 font-italic small text-secondary">"${invoiceFooter}"</p>
                </div>
                ` : ''}
            </div>
        </div>
    `;

    window.Swal.fire({
        title: '<i class="fas fa-id-card me-2 text-primary"></i>Perfil Completo de Empresa',
        html: content,
        width: '650px',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#4e73df',
        showCloseButton: true,
        customClass: {
            popup: 'rounded-4 border-0 shadow-lg'
        }
    });
}
