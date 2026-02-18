export default class DataGrid {
    constructor(elementId, options = {}) {
        this.elementId = elementId;
        this.instance = null;

        if (!options.url) {
            console.error('DataGrid: La URL del servidor es obligatoria para el modo server-side.');
            return;
        }

        // Inyectar automáticamente data-label en las columnas para el modo responsivo
        if (options.columns) {
            options.columns = options.columns.map(col => {
                const colName = typeof col === 'string' ? col : (col.name || '');
                const colObj = typeof col === 'string' ? { name: col } : { ...col };
                
                // Preservar atributos existentes si los hay
                const existingAttributes = colObj.attributes || {};
                
                colObj.attributes = (cell, row, column) => {
                    const attrs = typeof existingAttributes === 'function' 
                        ? existingAttributes(cell, row, column) 
                        : { ...existingAttributes };
                    
                    return {
                        ...attrs,
                        'data-label': colName
                    };
                };
                return colObj;
            });
        }

        this.options = {
            language: {
                'search': { 'placeholder': 'Buscar...' },
                'pagination': {
                    'previous': 'Anterior',
                    'next': 'Siguiente',
                    'showing': 'Mostrando',
                    'results': () => 'registros'
                },
                'noRecordsFound': 'No se encontraron registros',
                'loading': 'Cargando...',
            },
            className: {
                table: 'table table-hover',
                thead: 'bg-light',
                th: 'py-3 text-secondary text-uppercase small fw-bold',
                td: 'py-3 align-middle'
            },
            resizable: true,
            sort: true,
            // Configuración OBLIGATORIA de Server-Side Pagination
            pagination: {
                limit: options.limit || 10,
                server: {
                    url: (prev, page, limit) => {
                        const url = new URL(prev, prev.startsWith('http') ? undefined : window.location.origin);
                        url.searchParams.set('limit', limit);
                        url.searchParams.set('offset', page * limit);
                        return url.toString();
                    }
                }
            },
            // Configuración OBLIGATORIA de Server-Side Search
            search: {
                server: {
                    url: (prev, keyword) => {
                        if (!keyword) return prev;
                        const url = new URL(prev, prev.startsWith('http') ? undefined : window.location.origin);
                        url.searchParams.set('search', keyword);
                        return url.toString();
                    }
                }
            },
            // Configuración del Servidor
            server: {
                url: options.url,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                then: data => {
                    const records = data.data || [];
                    this.lastData = records;
                    return records.map(item => options.mapData ? options.mapData(item) : Object.values(item));
                },
                total: data => data.total || 0,
                handle: (res) => {
                    if (res.status === 404) return { data: [], total: 0 };
                    if (res.ok) return res.json();
                    
                    return res.text().then(text => {
                        console.error('Error del servidor (no es JSON):', text.substring(0, 200));
                        return { data: [], total: 0 };
                    });
                }
            },
            ...options
        };
    }

    render() {
        if (!window.Gridjs) {
            console.error('Grid.js is not loaded');
            return;
        }
        
        const container = document.getElementById(this.elementId);
        if (!container) {
            console.warn(`DataGrid: Container element with ID "${this.elementId}" not found. Skipping render.`);
            return;
        }

        this.instance = new window.Gridjs.Grid(this.options);
        this.instance.render(container);
        this.addExportButtons();
        return this.instance;
    }

    addExportButtons() {
        const wrapper = document.getElementById(this.elementId);
        if (!wrapper) return;

        const container = document.createElement('div');
        container.className = 'd-flex justify-content-end gap-2 mb-3 mt-2 export-buttons-container';
        container.innerHTML = `
            <button class="btn btn-sm btn-outline-danger rounded-pill px-3 export-pdf">
                <i class="fas fa-file-pdf me-1"></i> <span class="d-none d-sm-inline">PDF</span>
            </button>
            <button class="btn btn-sm btn-outline-success rounded-pill px-3 export-excel">
                <i class="fas fa-file-excel me-1"></i> <span class="d-none d-sm-inline">Excel</span>
            </button>
        `;

        wrapper.prepend(container);

        container.querySelector('.export-pdf').addEventListener('click', () => this.exportPDF());
        container.querySelector('.export-excel').addEventListener('click', () => this.exportExcel());
    }

    async getExportData() {
        // En modo server-side, intentamos obtener todos los datos (o los actuales cargados)
        return this.lastData || [];
    }

    async exportPDF() {
        const data = await this.getExportData();
        const columns = this.options.columns.filter(col => typeof col === 'string' || (col.name && col.name !== 'Acciones'));
        const columnNames = columns.map(col => typeof col === 'string' ? col : col.name);
        
        const rows = data.map(item => {
            return columns.map(col => {
                const key = (typeof col === 'object') ? (col.id || col.data || col.name.toLowerCase()) : col.toLowerCase();
                let value = item[key];
                if (value === undefined || value === null) {
                    const nameKey = (typeof col === 'object') ? col.name : col;
                    value = item[nameKey];
                }
                return value !== undefined && value !== null ? value : '';
            });
        });

        // Dynamic Import
        const { jsPDF } = await import('jspdf');
        const autoTable = (await import('jspdf-autotable')).default;

        const doc = new jsPDF();
        doc.text("Reporte de " + (document.title || 'Datos'), 14, 15);
        
        const autoTableConfig = {
            head: [columnNames],
            body: rows,
            startY: 20,
            theme: 'grid',
            headStyles: { fillStyle: [78, 115, 223] }
        };

        autoTable(doc, autoTableConfig);
        doc.save(`${document.title || 'reporte'}.pdf`);
    }

    async exportExcel() {
        const data = await this.getExportData();
        const columns = this.options.columns.filter(col => typeof col === 'string' || (col.name && col.name !== 'Acciones'));
        
        const rows = data.map(item => {
            const row = {};
            columns.forEach(col => {
                const name = (typeof col === 'object') ? col.name : col;
                const key = (typeof col === 'object') ? (col.id || col.data || col.name.toLowerCase()) : col.toLowerCase();
                
                let value = item[key];
                if (value === undefined || value === null) {
                    value = item[name];
                }
                
                row[name] = value !== undefined && value !== null ? value : '';
            });
            return row;
        });

        // Dynamic Import
        const XLSX = await import('xlsx');

        const worksheet = XLSX.utils.json_to_sheet(rows);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Datos");
        XLSX.writeFile(workbook, `${document.title || 'reporte'}.xlsx`);
    }

    static html(content) {
        if (!window.Gridjs) {
            console.error('Grid.js is not loaded');
            return content;
        }
        return window.Gridjs.html(content);
    }
}
