import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import '@fortawesome/fontawesome-free/js/all.js';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import { Grid, html } from 'gridjs';
import Swal from 'sweetalert2';

// Import Modules
import DataGrid from './modules/DataGrid';
import Notifications from './modules/Notifications';
import { initUI } from './initUI';
import { initPageLoader } from './PageLoader';
import { initPWA } from './pwa-handler';



// ==========================================
// GLOBAL ASSIGNMENTS
// ==========================================

// Libraries
window.Alpine = Alpine;
window.TomSelect = TomSelect;
window.Gridjs = { Grid, html };
window.Swal = Swal;

// Modules
window.DataGrid = DataGrid;
window.Notify = Notifications;

// Initialize
Alpine.start();
initUI();
initPageLoader(); // Inicialización automática de páginas
initPWA();
