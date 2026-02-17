import Chart from 'chart.js/auto';

export const initProductosChart = (data) => {
    const ctx = document.getElementById('productosChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(p => p.product_name),
            datasets: [{
                label: 'Unidades',
                data: data.map(p => p.total_vendido),
                backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                borderRadius: 10,
                barThickness: 20
            }]
        },
        options: { 
            indexAxis: 'y',
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { grid: { display: false }, border: { display: false } }
            }
        }
    });
};
