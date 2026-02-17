/**
 * PageLoader.js
 * Sistema de inicialización automática basado en selectores del DOM.
 * Elimina la necesidad de scripts inline en Blade.
 */

// Importamos todas las funciones de inicialización
import { initUsersIndex } from './pages/tenants/usuarios/index';
import { initCategoriesIndex } from './pages/tenants/categories/index';
import { initClientsIndex } from './pages/tenants/clients/index';
import { initProductsIndex } from './pages/tenants/products/index';
import { initPurchasesIndex } from './pages/tenants/purchases/index';
import { initSuppliersIndex } from './pages/tenants/suppliers/index';
import { initCashRegistersIndex } from './pages/tenants/cash_registers/index';
import { initSalesIndex } from './pages/tenants/sales/index';
import { initSalesPOS } from './pages/tenants/sales/pos';
import { initAbonosIndex } from './pages/tenants/abonos/index';
import { initAbonosCreate } from './pages/tenants/abonos/create';
import { initReportsIndex } from './pages/tenants/reports/index';
import { initRolesIndex } from './pages/tenants/roles/index';

// Central Pages
import { initUsersIndex as initCentralUsersIndex } from './pages/central/users/index';
import { initTenantsIndex } from './pages/central/tenants/index';
import { initPaymentNotificationsIndex } from './pages/central/payment-notifications/index';

const loaders = {
    // Tenant Pages
    '#users-index-page': initUsersIndex,
    '#categories-index-page': initCategoriesIndex,
    '#clients-index-page': initClientsIndex,
    '#products-index-page': initProductsIndex,
    '#purchases-index-page': initPurchasesIndex,
    '#suppliers-index-page': initSuppliersIndex,
    '#cash-registers-index-page': initCashRegistersIndex,
    '#sales-index-page': initSalesIndex,
    '#sales-pos-page': initSalesPOS,
    '#abonos-index-page': initAbonosIndex,
    '#abonos-create-page': initAbonosCreate,
    '#reports-index-page': initReportsIndex,
    '#roles-index-page': initRolesIndex,

    // Central Pages
    '#central-users-index-page': initCentralUsersIndex,
    '#central-tenants-index-page': initTenantsIndex,
    '#central-payment-notifications-page': initPaymentNotificationsIndex
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
