
const moduleNames = {
    'categories': 'Categorías',
    'clients': 'Clientes',
    'suppliers': 'Proveedores',
    'products': 'Productos',
    'purchases': 'Compras'
};

export function initImportIndex(config) {
    // Exponer funciones globalmente para que funcionen los onclick del HTML
    window.openImportModal = openImportModal;
    window.submitImport = submitImport;

    // Manejar z-index dinámico para los dropdowns
    const dropdownElements = document.querySelectorAll('.dropdown');
    
    dropdownElements.forEach(dropdown => {
        dropdown.addEventListener('show.bs.dropdown', function () {
            const card = this.closest('.card');
            if (card) {
                card.classList.add('card-dropdown-open');
            }
        });

        dropdown.addEventListener('hide.bs.dropdown', function () {
            const card = this.closest('.card');
            if (card) {
                card.classList.remove('card-dropdown-open');
            }
        });
    });
}

function openImportModal(module) {
    const importModule = document.getElementById('importModule');
    const modalModuleName = document.getElementById('modalModuleName');
    const importForm = document.getElementById('importForm');
    const formFields = document.getElementById('formFields');
    const importProgress = document.getElementById('importProgress');
    const importResult = document.getElementById('importResult');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancel = document.getElementById('btnCancel');
    const importModal = document.getElementById('importModal');

    if (!importModule || !modalModuleName || !importForm || !formFields || !importProgress || !importResult || !btnSubmit || !btnCancel || !importModal) {
        console.error('Elementos del modal de importación no encontrados');
        return;
    }

    importModule.value = module;
    modalModuleName.textContent = moduleNames[module];
    importForm.reset();
    formFields.classList.remove('d-none');
    importProgress.classList.add('d-none');
    importResult.classList.add('d-none');
    
    // Reset footer
    btnSubmit.classList.remove('d-none');
    btnCancel.textContent = 'Cancelar';
    btnCancel.classList.remove('d-none');
    
    const modal = new bootstrap.Modal(importModal);
    modal.show();
}

async function submitImport() {
    const form = document.getElementById('importForm');
    const formData = new FormData(form);
    const module = document.getElementById('importModule').value;
    const formFields = document.getElementById('formFields');
    const progressDiv = document.getElementById('importProgress');
    const resultDiv = document.getElementById('importResult');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnCancel = document.getElementById('btnCancel');

    // Validar archivo
    if (!formData.get('file') || !formData.get('file').name) {
        Notify.error('Error', 'Por favor selecciona un archivo');
        return;
    }

    // Preparar UI
    formFields.classList.add('d-none');
    progressDiv.classList.remove('d-none');
    resultDiv.classList.add('d-none');
    btnSubmit.classList.add('d-none');
    
    progressBar.style.width = '30%';
    progressBar.textContent = '30%';

    try {
        // Obtener el token CSRF desde el meta tag o input hidden si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="_token"]')?.value;

        const response = await fetch(`/import/${module}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        });

        progressBar.style.width = '70%';
        progressBar.textContent = '70%';

        const result = await response.json();
        progressBar.style.width = '100%';
        progressBar.textContent = '100%';

        if (response.status === 422) {
            let errorMessage = result.message || 'Error de validación';
            if (result.errors) {
                const errors = Object.values(result.errors).flat().join('<br>');
                errorMessage = `<strong>${errorMessage}</strong>:<br>${errors}`;
            }
            throw new Error(errorMessage);
        }

        setTimeout(() => {
            progressDiv.classList.add('d-none');
            
            if (response.status === 200 && result.status === 'success') {
                resultDiv.innerHTML = `
                    <div class="alert ${result.created > 0 ? 'alert-success' : 'alert-warning'} border-0 shadow-sm">
                        <h6 class="alert-heading fw-bold"><i class="fas ${result.created > 0 ? 'fa-check-circle' : 'fa-exclamation-circle'} me-1"></i> Resumen de Importación</h6>
                        <p class="mb-1"><strong>${result.created}</strong> registro(s) creado(s) con éxito.</p>
                        ${result.duplicates > 0 ? `<p class="mb-1 small"><strong>${result.duplicates}</strong> registro(s) omitido(s) por duplicados.</p>` : ''}
                        
                        ${result.errors > 0 ? `
                            <div class="mt-3 p-2 bg-white rounded border">
                                <p class="mb-1 small text-danger fw-bold"><i class="fas fa-times-circle me-1"></i> ${result.errors} Errores encontrados:</p>
                                <ul class="small text-danger mb-0 text-start" style="max-height: 200px; overflow-y: auto;">
                                    ${result.error_messages.map(msg => `<li>${msg}</li>`).join('')}
                                </ul>
                            </div>
                        ` : ''}
                    </div>
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal" onclick="window.location.reload()">Aceptar y Recargar</button>
                    </div>
                `;
                resultDiv.classList.remove('d-none');
                btnCancel.classList.add('d-none'); // Ocultar el cancelar original
                
                if (result.errors === 0) {
                    Notify.success('Éxito', 'Importación completada correctamente');
                } else {
                    Notify.warning('Importación Parcial', 'Se completó con algunos errores');
                }
            } else {
                throw new Error(result.message || 'Error al importar');
            }
        }, 500);

    } catch (error) {
        progressDiv.classList.add('d-none');
        formFields.classList.remove('d-none');
        btnSubmit.classList.remove('d-none');
        
        resultDiv.innerHTML = `
            <div class="alert alert-danger border-0 shadow-sm">
                <h6 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Error de Sistema</h6>
                <div class="mb-0 small">${error.message}</div>
            </div>
        `;
        resultDiv.classList.remove('d-none');
        Notify.error('Error', 'No se pudo completar la operación');
    }
}
