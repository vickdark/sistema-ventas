/**
 * PageLoader.js
 * Sistema de inicialización automática basado en selectores del DOM.
 * Utiliza Lazy Loading (importaciones dinámicas) para optimizar la carga.
 */

const loaders = {
    // Tenant Pages
    '#forgot-password-page': () => import('./pages/tenants/auth/forgot-password').then(m => m.initForgotPassword),
    '#users-index-page': () => import('./pages/tenants/usuarios/index').then(m => m.initUsersIndex),
    '#categories-index-page': () => import('./pages/tenants/categories/index').then(m => m.initCategoriesIndex),
    '#categories-create-page': () => import('./pages/tenants/categories/create').then(m => m.initCategoriesCreate),
    '#clients-index-page': () => import('./pages/tenants/clients/index').then(m => m.initClientsIndex),
    '#clients-create-page': () => import('./pages/tenants/clients/create').then(m => m.initClientsCreate),
    '#import-index-page': () => import('./pages/tenants/import/index').then(m => m.initImportIndex),
    '#products-index-page': () => import('./pages/tenants/products/index').then(m => m.initProductsIndex),
    '#products-create-page': () => import('./pages/tenants/products/create').then(m => m.initProductsCreate),
    '#products-edit-page': () => import('./pages/tenants/products/edit').then(m => m.initProductsEdit),
    '#purchases-index-page': () => import('./pages/tenants/purchases/index').then(m => m.initPurchasesIndex),
    '#purchases-create-page': () => import('./pages/tenants/purchases/create').then(m => m.initPurchasesCreate),
    '#purchases-edit-page': () => import('./pages/tenants/purchases/edit').then(m => m.initPurchasesEdit),
    '#suppliers-index-page': () => import('./pages/tenants/suppliers/index').then(m => m.initSuppliersIndex),
    '#suppliers-create-page': () => import('./pages/tenants/suppliers/create').then(m => m.initSuppliersCreate),
    '#cash-registers-index-page': () => import('./pages/tenants/cash_registers/index').then(m => m.initCashRegistersIndex),
    '#sales-index-page': () => import('./pages/tenants/sales/index').then(m => m.initSalesIndex),
    '#sales-pos-page': () => import('./pages/tenants/sales/pos/index').then(m => m.initSalesPOS),
    '#abonos-index-page': () => import('./pages/tenants/abonos/index').then(m => m.initAbonosIndex),
    '#abonos-create-page': () => import('./pages/tenants/abonos/create').then(m => m.initAbonosCreate),
    '#reports-index-page': () => import('./pages/tenants/reports/index').then(m => m.initReportsIndex),
    '#roles-index-page': () => import('./pages/tenants/roles/index').then(m => m.initRolesIndex),
    '#role-permissions-page': () => import('./pages/tenants/roles/permissions').then(m => m.initRolePermissions),

    // Central Pages
    '#central-login-page': () => import('./pages/central/auth/login').then(m => m.initCentralLogin),
    '#central-users-index-page': () => import('./pages/central/users/index').then(m => m.initUsersIndex),
    '#central-tenants-index-page': () => import('./pages/central/tenants/index').then(m => m.initTenantsIndex),
    '#central-tenants-create-page': () => import('./pages/central/tenants/create').then(m => m.initTenantsCreate),
    '#central-tenants-edit-page': () => import('./pages/central/tenants/edit').then(m => m.initTenantsEdit),
    '#central-payment-notifications-page': () => import('./pages/central/payment-notifications/index').then(m => m.initPaymentNotificationsIndex)
};

export function initPageLoader() {
    document.addEventListener('DOMContentLoaded', async () => {
        // Buscar qué página estamos viendo
        for (const [selector, importFunction] of Object.entries(loaders)) {
            const element = document.querySelector(selector);
            if (element) {
                // Leer configuración desde data-config
                const configStr = element.getAttribute('data-config');
                let config = {};
                
                if (configStr) {
                    try {
                        config = JSON.parse(configStr);
                    } catch (e) {
                        console.error(`PageLoader: Error parsing JSON config for ${selector}`, e);
                        console.error('Invalid JSON:', configStr);
                    }
                }

                // Inyectar CSRF token global si no viene en config
                if (!config.tokens) config.tokens = {};
                if (!config.tokens.csrf) {
                    const metaCsrf = document.querySelector('meta[name="csrf-token"]');
                    if (metaCsrf) config.tokens.csrf = metaCsrf.content;
                }

                // Ejecutar inicializador con Lazy Loading
                console.log(`PageLoader: Loading module for ${selector}...`);
                try {
                    const initFunction = await importFunction();
                    console.log(`PageLoader: Initializing ${selector}`);
                    if (typeof initFunction === 'function') {
                        initFunction(config);
                    } else {
                        console.error(`PageLoader: Module for ${selector} does not export a function.`);
                    }
                } catch (error) {
                    console.error(`PageLoader: Failed to load module for ${selector}`, error);
                }
                
                return; // Solo una página activa a la vez
            }
        }
    });
}
