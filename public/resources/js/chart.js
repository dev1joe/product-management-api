
/**
 * @param {string} type
 */
export function createChart(type) {
    const widget = document.createElement('div');
    const chartElement = document.createElement('canvas');

    new Chart(chartElement, {
        type: type,
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    return chartElement;
};

/**
 * @param {HTMLElement} container
 */
export function loadAnalytics(container) {
    console.log('loading analytics....... ');
    container.appendChild(createChart('bar'));
    container.appendChild(createChart('line'));
    //TODO: control charts somehow
}

const chartsContainer = document.getElementById('charts-container');
window.onload = function() {
    loadAnalytics(chartsContainer);
}