import Notifications from '../../../modules/Notifications';
import TomSelect from 'tom-select';

export class CustomerManager {
    constructor(config) {
        this.routes = config.routes;
        this.tokens = config.tokens;
        
        this.clientSelect = null;
        this.btnSaveQuickClient = document.getElementById('btnSaveQuickClient');
        this.quickClientForm = document.getElementById('quickClientForm');
        this.quickClientModal = document.getElementById('quickClientModal') ? new bootstrap.Modal(document.getElementById('quickClientModal')) : null;
        this.btnOpenQuickClient = document.querySelector('[title="Nuevo Cliente"]');
        
        this.init();
    }

    init() {
        // Initialize TomSelect
        this.clientSelect = new TomSelect('#client_id', {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: 'Seleccione cliente...'
        });

        // Quick Client Events
        if (this.btnOpenQuickClient && this.quickClientModal) {
            this.btnOpenQuickClient.addEventListener('click', () => this.quickClientModal.show());
        }

        if (this.btnSaveQuickClient) {
            this.btnSaveQuickClient.addEventListener('click', () => this.saveQuickClient());
        }
    }

    async saveQuickClient() {
        const formData = new FormData(this.quickClientForm);
        const data = Object.fromEntries(formData.entries());

        if (!data.name || !data.nit_ci) {
            Notifications.error('Campos requeridos', 'Nombre y NIT/CI son obligatorios.');
            return;
        }

        this.btnSaveQuickClient.disabled = true;
        this.btnSaveQuickClient.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> GUARDANDO...';

        try {
            const response = await fetch(this.routes.clients_store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.tokens.csrf,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                Notifications.success('Cliente creado', 'El cliente fue registrado exitosamente.');
                
                // Add to TomSelect and select it
                this.clientSelect.addOption({ value: result.data.id, text: `${result.data.name} (${result.data.nit_ci})` });
                this.clientSelect.setValue(result.data.id);
                
                this.quickClientModal.hide();
                this.quickClientForm.reset();
            } else {
                Notifications.error('Error', result.message || 'No se pudo crear el cliente.');
            }
        } catch (error) {
            Notifications.error('Error de conexión');
            console.error(error);
        } finally {
            this.btnSaveQuickClient.disabled = false;
            this.btnSaveQuickClient.innerHTML = 'Guardar Cliente';
        }
    }

    getSelectedClientId() {
        return this.clientSelect.getValue();
    }

    getSelectedClientName() {
        const id = this.getSelectedClientId();
        return this.clientSelect.getItem(id)?.textContent || 'Consumidor Final';
    }
}
