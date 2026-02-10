import './bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import '@fortawesome/fontawesome-free/js/all.js';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import { Grid, html } from 'gridjs';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

// Import Modules
import DataGrid from './modules/DataGrid';
import { initSidebar } from './modules/sidebar';
import Notifications from './modules/Notifications';

// Import Pages
import { initUsersIndex } from './pages/usuarios/index';
import { initTenantsIndex } from './pages/central/tenants/index';
import { initCategoriesIndex } from './pages/categories/index';
import { initClientsIndex } from './pages/clients/index';
import { initProductsIndex } from './pages/products/index';
import { initPurchasesIndex } from './pages/purchases/index';
import { initSuppliersIndex } from './pages/suppliers/index';
import { initCashRegistersIndex } from './pages/cash_registers/index';
import { initSalesIndex } from './pages/sales/index';
import { initSalesPOS } from './pages/sales/pos';
import { initAbonosIndex } from './pages/abonos/index';
import { initAbonosCreate } from './pages/abonos/create';

//Opciones de expotacion para Grid JS 
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';


// Global Assignments
window.Alpine = Alpine;
window.TomSelect = TomSelect;
window.Gridjs = { Grid, html };
window.Swal = Swal;
window.Chart = Chart;
window.jsPDF = jsPDF;
window.autoTable = autoTable;
window.XLSX = XLSX;
window.DataGrid = DataGrid;
window.Notify = Notifications;
window.initUsersIndex = initUsersIndex;
window.initTenantsIndex = initTenantsIndex;
window.initCategoriesIndex = initCategoriesIndex;
window.initClientsIndex = initClientsIndex;
window.initProductsIndex = initProductsIndex;
window.initPurchasesIndex = initPurchasesIndex;
window.initSuppliersIndex = initSuppliersIndex;
window.initCashRegistersIndex = initCashRegistersIndex;
window.initSalesIndex = initSalesIndex;
window.initSalesPOS = initSalesPOS;
window.initAbonosIndex = initAbonosIndex;
window.initAbonosCreate = initAbonosCreate;

// Initialize
Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
});
