<div class="row">
    <div class="col-md-6 mb-4">
        <!-- Biểu đồ -->
        <canvas id="employeeRatioChart"></canvas>
    </div>
    <div class="col-md-6">
        <h2 style="font-weight: bold">Biểu đồ hiển thị tỉ lệ phòng ban</h2>
        <!-- Hiển thị tổng số nhân viên -->
        <p style="font-size: 23px;" id="totalEmployees">Tổng nhân sự từ các phòng ban: đang tính...
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    async function loadTotalEmployees() {
        const response = await fetch('/api/total-employees');
        const data = await response.json();

        document.getElementById('totalEmployees').textContent = `Tổng các nhân sự: ${data.totalEmployees}`;
    }

    // Gọi hàm khi trang tải
    loadTotalEmployees();
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
{{-- <style>
    #employeeRatioChart {
        width: 100%;
        /* Chiều rộng mặc định */
        max-width: 600px;
        /* Đặt giới hạn chiều rộng tối đa */
        height: 400px;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Chiều cao cụ thể */
    }
</style> --}}
