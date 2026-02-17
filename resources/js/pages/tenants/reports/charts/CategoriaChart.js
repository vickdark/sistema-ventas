import Chart from 'chart.js/auto';

export const initCategoriaChart = (data) => {
    const ctx = document.getElementById('categoriaChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(c => c.name),
            datasets: [{
                data: data.map(c => c.products_count),
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            cutout: '70%',
            plugins: { 
                legend: { 
                    position: 'right',
                    labels: { usePointStyle: true, padding: 20 }
                } 
            } 
        }
    });
};
