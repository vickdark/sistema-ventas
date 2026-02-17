import Chart from 'chart.js/auto';
import { getGradient } from '../utils/ChartConfig';

export const initVentasChart = (data) => {
    const ctx = document.getElementById('ventasChart');
    if (!ctx) return;

    const context = ctx.getContext('2d');
    const gradient = getGradient(context, 'rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(v => v.fecha),
            datasets: [{
                label: 'Ingresos ($)',
                data: data.map(v => v.total),
                borderColor: '#10b981',
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            }
        }
    });
};
