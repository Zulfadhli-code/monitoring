import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('chart');
    if (!canvas) return;

    fetch('/chart-data')
        .then(res => res.json())
        .then(data => {
            new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['Ready', 'Maintenance', 'Down'],
                    datasets: [{
                        data: [
                            data.ready ?? 0,
                            data.maintenance ?? 0,
                            data.down ?? 0
                        ],
                        backgroundColor: ['green', 'orange', 'red']
                    }]
                }
            });
        });
});