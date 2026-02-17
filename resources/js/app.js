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
import Notifications from './modules/Notifications';
import { initUI } from './initUI';
import { initPageLoader } from './PageLoader';

// Opciones de exportación para Grid JS 
import { jsPDF } from 'jspdf';
import autoTable from 'jspdf-autotable';
import * as XLSX from 'xlsx';


// ==========================================
// GLOBAL ASSIGNMENTS
// ==========================================

// Libraries
window.Alpine = Alpine;
window.TomSelect = TomSelect;
window.Gridjs = { Grid, html };
window.Swal = Swal;
window.Chart = Chart;
window.jsPDF = jsPDF;
window.autoTable = autoTable;
window.XLSX = XLSX;

// Modules
window.DataGrid = DataGrid;
window.Notify = Notifications;

// Initialize
Alpine.start();
initUI();
initPageLoader(); // Inicialización automática de páginas
