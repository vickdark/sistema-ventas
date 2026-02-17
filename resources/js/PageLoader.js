/**
 * PageLoader.js
 * Sistema de inicialización automática basado en selectores del DOM.
 * Elimina la necesidad de scripts inline en Blade.
 */

// Importamos todas las funciones de inicialización desde el índice consolidado
import * as TenantPages from './pages/tenants/index';

// Central Pages
import { initUsersIndex as initCentralUsersIndex } from './pages/central/users/index';
import { initTenantsIndex } from './pages/central/tenants/index';
import { initPaymentNotificationsIndex } from './pages/central/payment-notifications/index';

const loaders = {
    // Tenant Pages
    '#forgot-password-page': TenantPages.initForgotPassword,
    '#users-index-page': TenantPages.initUsersIndex,
    '#categories-index-page': TenantPages.initCategoriesIndex,
    '#categories-create-page': TenantPages.initCategoriesCreate,
    '#clients-index-page': TenantPages.initClientsIndex,
    '#clients-create-page': TenantPages.initClientsCreate,
    '#import-index-page': TenantPages.initImportIndex,
    '#products-index-page': TenantPages.initProductsIndex,
    '#products-create-page': TenantPages.initProductsCreate,
    '#products-edit-page': TenantPages.initProductsEdit,
    '#purchases-index-page': TenantPages.initPurchasesIndex,
    '#purchases-create-page': TenantPages.initPurchasesCreate,
    '#suppliers-index-page': TenantPages.initSuppliersIndex,
    '#suppliers-create-page': TenantPages.initSuppliersCreate,
    '#cash-registers-index-page': TenantPages.initCashRegistersIndex,
    '#sales-index-page': TenantPages.initSalesIndex,
    '#sales-pos-page': TenantPages.initSalesPOS,
    '#abonos-index-page': TenantPages.initAbonosIndex,
    '#abonos-create-page': TenantPages.initAbonosCreate,
    '#reports-index-page': TenantPages.initReportsIndex,
    '#roles-index-page': TenantPages.initRolesIndex,
    '#role-permissions-page': TenantPages.initRolePermissions,

    // Central Pages
    '#central-login-page': CentralPages.initCentralLogin,
    '#central-users-index-page': CentralPages.initUsersIndex,
    '#central-tenants-index-page': CentralPages.initTenantsIndex,
    '#central-payment-notifications-page': CentralPages.initPaymentNotificationsIndex
};

export function initPageLoader() {
    document.addEventListener('DOMContentLoaded', () => {
        // Buscar qué página estamos viendo
        for (const [selector, initFunction] of Object.entries(loaders)) {
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

                // Ejecutar inicializador
                console.log(`PageLoader: Initializing ${selector}`);
                initFunction(config);
                return; // Solo una página activa a la vez
            }
        }
    });
}
