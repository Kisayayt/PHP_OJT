<div class="row">
    <h4 style="font-weight: bold" class="text-center mb-3 mt-3">Thống kê ngày công hợp lệ và không hợp lệ</h4>
    <div class="text-center mb-3">
        <button class="btn btn-primary filter-btn" data-filter="7">7 ngày trước</button>
        <button class="btn btn-primary filter-btn" data-filter="30">30 ngày trước</button>
        <button class="btn btn-primary filter-btn" data-filter="365">12 tháng</button>
    </div>
    <div class="chart-container" style="position: relative; max-width: 800px; margin: 0 auto;">
        <canvas id="attendanceChart"></canvas>
    </div>
</div>

<style>
    .chart-container {
        position: relative;
        max-width: 800px;
        height: 400px;
        margin: 0 auto;
    }

    #attendanceChart {
        background-color: #f8f9fa;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>




<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let chartInstance; // Khai báo biến toàn cục để quản lý biểu đồ

    async function loadAttendanceStats(filterDays = null) {
        const url = filterDays ? `/attendance-stats?filter=${filterDays}` : '/attendance-stats';
        const response = await fetch(url);
        const data = await response.json();

        // Xử lý dữ liệu trả về để lấy danh sách ngày và giá trị ngày công
        const labels = data.map(item => item.day); // Trục X là danh sách ngày
        const validDays = data.map(item => item.valid_days); // Ngày công hợp lệ theo ngày
        const invalidDays = data.map(item => item.invalid_days); // Ngày công không hợp lệ theo ngày

        // Dữ liệu cho biểu đồ
        const chartData = {
            labels: labels,
            datasets: [{
                    label: 'Ngày công hợp lệ',
                    data: validDays,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Ngày công không hợp lệ',
                    data: invalidDays,
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4,
                },
            ],
        };

        // Cấu hình biểu đồ
        const config = {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Thống kê ngày công theo ngày',
                    },
                },
                interaction: {
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Ngày',
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số ngày công',
                        },
                    },
                },
            },
        };

        // Xóa biểu đồ cũ (nếu có) trước khi tạo biểu đồ mới
        if (chartInstance) {
            chartInstance.destroy();
        }

        // Tạo biểu đồ mới
        const chartContainer = document.getElementById('attendanceChart');
        chartInstance = new Chart(chartContainer, config);
    }

    // Lắng nghe sự kiện click trên các nút lọc
    document.querySelectorAll('.filter-btn').forEach((button) => {
        button.addEventListener('click', function() {
            const filterDays = this.getAttribute('data-filter');
            loadAttendanceStats(filterDays);
        });
    });

    // Gọi hàm loadAttendanceStats khi trang được tải
    loadAttendanceStats();
</script>
