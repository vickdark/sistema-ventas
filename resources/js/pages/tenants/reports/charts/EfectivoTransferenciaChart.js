import Chart from 'chart.js/auto';

export const initEfectivoTransferenciaChart = (data) => {
    const ctx = document.getElementById('cashTransferChart');
    if (!ctx || !data) return;

    const labels = data.map(e => {
        if (e.payment_type === 'CONTADO') return 'Efectivo';
        if (e.payment_type === 'TRANSFERENCIA') return 'Transferencia';
        return e.payment_type;
    });
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data.map(e => e.total),
                backgroundColor: ['#10b981', '#6366f1'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 15
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '65%',
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: { 
                        usePointStyle: true, 
                        padding: 20,
                        font: { size: 13, weight: '600' }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                } 
            } 
        }
    });
};
