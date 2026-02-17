import Chart from 'chart.js/auto';

export const initMetodosPagoChart = (data) => {
    const ctx = document.getElementById('metodosChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(m => m.payment_type),
            datasets: [{
                data: data.map(m => m.total),
                backgroundColor: ['#10b981', '#6366f1', '#fb923c', '#ef4444'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            plugins: { 
                legend: { 
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 15 }
                } 
            } 
        }
    });
};
