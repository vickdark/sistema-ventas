import { initBilling } from './create/billing.js';
import { initDatabase } from './create/database.js';
import { initValidation } from './create/validation.js';
import { initForm } from './create/form.js';

export function initTenantsCreate(config) {
    if (typeof window.bootstrap === 'undefined') {
        console.warn('Bootstrap is not loaded yet. Some features might not work.');
    }

    initBilling();
    initDatabase();
    initValidation(config);
    initForm(config);
    
    console.log('Tenants Create Page Initialized');
}
