import Chart from 'chart.js/auto';

export const initCajaChart = (data) => {
    const ctx = document.getElementById('cajaChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(c => `${c.date} (${c.name})`),
            datasets: [
                { 
                    label: 'M. Inicial', 
                    data: data.map(c => c.initial_amount), 
                    backgroundColor: '#cbd5e1',
                    borderRadius: 5
                },
                { 
                    label: 'M. Final', 
                    data: data.map(c => c.final_amount), 
                    backgroundColor: '#6366f1',
                    borderRadius: 5
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            const item = data[index];
                            return `Sesión: ${item.name}`;
                        },
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(context.parsed.y);
                            }
                            return label;
                        },
                        afterBody: function(context) {
                            const index = context[0].dataIndex;
                            const item = data[index];
                            return `Fecha: ${item.date}`;
                        }
                    }
                }
            },
            scales: {
                y: { 
                    grid: { color: 'rgba(0,0,0,0.05)' }, 
                    border: { display: false },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
};
