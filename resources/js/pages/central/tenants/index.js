export function initTenantsIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("tenants-grid", {
        url: routes.index,
        columns: [
            { id: 'id', name: "Empresa / ID", width: "200px" },
            { 
                id: 'domain', 
                name: "Dominio de Acceso",
                formatter: (cell) => DataGrid.html(`<a href="http://${cell}" target="_blank" class="text-primary text-decoration-none fw-bold"><i class="fas fa-external-link-alt me-1 small"></i>${cell}</a>`)
            },
            { 
                id: 'database', 
                name: "Base de Datos",
                formatter: (cell) => DataGrid.html(`<span class="badge bg-light text-dark border"><i class="fas fa-database me-1 small opacity-50"></i>${cell}</span>`)
            },
            { 
                id: 'status', 
                name: "Estado de Cuenta",
                formatter: (cell, row) => {
                    const isPaid = cell === true || cell === 1 || cell === '1';
                    
                    let badgeClass = isPaid ? 'bg-success' : 'bg-danger';
                    let text = isPaid ? 'ACTIVO' : 'SUSPENDIDO';
                    let icon = isPaid ? 'fa-check-circle' : 'fa-ban';
                    
                    return DataGrid.html(`
                        <span class="badge ${badgeClass} rounded-pill px-3 py-2 shadow-sm" style="font-size: 0.75rem;">
                            <i class="fas ${icon} me-1"></i>${text}
                        </span>
                    `);
                }
            },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const tenantData = cell; // El objeto completo pasado en mapData
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    const markPaidUrl = routes.markPaid.replace(':id', id);
                    const isPaid = tenantData.is_paid === true || tenantData.is_paid === 1 || tenantData.is_paid === '1';
                    
                    if (!tenantData || typeof tenantData !== 'object') {
                        console.error('Data de inquilino no disponible para el modal', tenantData);
                        return '';
                    }

                    // Asegurarnos de que el objeto se pase correctamente al modal
                    const tenantJson = JSON.stringify(tenantData).replace(/'/g, "&apos;");
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            ${!isPaid ? `
                                <button type="button" 
                                    class="btn btn-sm btn-outline-success rounded-pill me-2" 
                                    onclick="window.markTenantAsPaid('${markPaidUrl}', '${id}')"
                                    title="Marcar como Pagado">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                            <button type="button" 
                                class="btn btn-sm btn-outline-info rounded-pill me-2" 
                                onclick='window.showTenantDetails(${tenantJson})'
                                title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar Empresa">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button type="button" 
                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                onclick="window.deleteTenant('${deleteUrl}', '${id}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (t) => [
            t.id, 
            t.domains && t.domains.length > 0 ? t.domains[0].domain : 'N/A',
            t.tenancy_db_name || `${config.db_prefix}_${t.id}`,
            t.is_paid,
            t // Enviamos el objeto completo a la columna 'actions'
        ]
    }).render();

    window.showTenantDetails = function(tenant) {
        if (!tenant) return;
        
        // Stancl Tenancy suele poner los datos personalizados dentro de un objeto 'data'
        // o los mezcla en el nivel superior dependiendo de la serialización.
        // Verificamos ambos lugares.
        const d = tenant.data || {};
        
        // Función auxiliar para obtener datos de primer nivel o del objeto 'data'
        const getVal = (key, fallback = 'N/A') => {
            if (tenant[key] !== undefined && tenant[key] !== null) return tenant[key];
            if (d[key] !== undefined && d[key] !== null) return d[key];
            return fallback;
        };
        
        const isPaidVal = getVal('is_paid', true);
        const isPaid = isPaidVal === true || isPaidVal === 1 || isPaidVal === '1';
                       
        const rawServiceType = getVal('service_type', 'subscription');
        const serviceType = rawServiceType === 'subscription' ? 'Suscripción' : 'Compra / Mantenimiento';
        
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
                        <span class="fw-bold text-primary">${serviceType}</span>
                    </div>
                    <div class="col-md-6 border-bottom pb-2">
                        <label class="text-muted small d-block mb-0 font-weight-bold">Próximo Pago</label>
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
    };

    window.deleteTenant = async function(url, id) {
        // Primera confirmación
        const firstConfirmed = await Notify.confirm({
            title: '¿Eliminar Empresa?',
            text: `Estás a punto de eliminar la empresa "${id}". Esta acción es IRREVERSIBLE.`,
            confirmButtonText: 'Sí, continuar',
            confirmButtonColor: '#e74a3b'
        });

        if (!firstConfirmed) return;

        // Segunda confirmación con frase de seguridad
        const securityPhrase = "ELIMINAR MI EMPRESA";
        const { value: phraseInput } = await window.Swal.fire({
            title: 'Confirmación de Seguridad',
            html: `Para confirmar, por favor escribe exactamente la frase:<br><strong class="text-danger">${securityPhrase}</strong>`,
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Eliminar permanentemente',
            confirmButtonColor: '#e74a3b',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return '¡Debes escribir la frase para continuar!';
                }
                if (value !== securityPhrase) {
                    return 'La frase no coincide. Inténtalo de nuevo.';
                }
            }
        });

        if (phraseInput === securityPhrase) {
            try {
                Notify.loading('Eliminando inquilino y recursos...');
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
                    Notify.success('Eliminado', 'La empresa ha sido eliminada correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar la empresa.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };

    window.markTenantAsPaid = async function(url, id) {
        const confirmed = await Notify.confirm({
            title: '¿Confirmar Pago?',
            text: `¿Estás seguro de marcar la empresa "${id}" como PAGADA? Esto extenderá su fecha de vencimiento según su plan.`,
            confirmButtonText: 'Sí, confirmar pago',
            confirmButtonColor: '#1cc88a'
        });

        if (confirmed) {
            try {
                Notify.loading('Actualizando estado de pago...');
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('¡Pago Confirmado!', result.message);
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo actualizar el pago.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
