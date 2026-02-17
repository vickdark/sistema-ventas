import Chart from 'chart.js/auto';

export const initBalanceChart = (data) => {
    const ctx = document.getElementById('balanceChart');
    if (!ctx) return;

    const labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                { 
                    label: 'Ingresos ($)', 
                    data: data.map(b => b.ingresos), 
                    backgroundColor: '#6366f1', 
                    borderRadius: 6,
                    barPercentage: 0.6
                },
                { 
                    label: 'Egresos ($)', 
                    data: data.map(b => b.egresos), 
                    backgroundColor: '#f43f5e', 
                    borderRadius: 6,
                    barPercentage: 0.6
                }
            ]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            scales: { 
                y: { grid: { color: 'rgba(0,0,0,0.05)' }, border: { display: false } },
                x: { grid: { display: false }, border: { display: false } }
            } 
        }
    });
};
