import { initBilling } from './edit/billing.js';
import { initActions } from './edit/actions.js';
import { initMaintenance } from './edit/maintenance.js';

export function initTenantsEdit(config) {
    if (typeof window.bootstrap === 'undefined') {
        console.warn('Bootstrap is not loaded yet. Some features might not work.');
    }

    initBilling();
    initActions(config);
    initMaintenance(config);
    
    console.log('Tenants Edit Page Initialized');
}
