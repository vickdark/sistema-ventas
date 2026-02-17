import { initSidebar } from './modules/sidebar';
import NavbarNotifications from './components/NavbarNotifications';
import { initPWA } from './pwa-handler';

export function initUI() {
    document.addEventListener('DOMContentLoaded', () => {
        // Inicializar Sidebar
        initSidebar();
        
        // Inicializar Notificaciones de Stock Bajo (Solo para Tenants)
        if (window.TenantConfig && window.TenantConfig.routes && window.TenantConfig.routes.low_stock) {
            new NavbarNotifications(window.TenantConfig.routes.low_stock);
        }
        
        // Inicializar PWA
        initPWA();
    });
}
