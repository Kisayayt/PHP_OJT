<div class="col-md-6 mb-4">
    <canvas id="employeeRatioChart"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    async function loadEmployeeRatioChart() {
        const response = await fetch('/api/employee-ratio-by-department');
        const data = await response.json();

        new Chart(document.getElementById('employeeRatioChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Tỉ lệ nhân sự',
                    data: data.counts,
                    backgroundColor: ['#FF6384'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Phòng ban',
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng nhân sự',
                        }
                    }
                }
            }
        });
    }

    loadEmployeeRatioChart();
</script>
