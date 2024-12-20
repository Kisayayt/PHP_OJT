<div class="container">
    <h4 class="text-center mb-3 mt-3">Thống kê ngày công hợp lệ và không hợp lệ</h4>
    <canvas id="attendanceChart" style="width: 100%; height: 400px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    async function loadAttendanceStats() {
        const response = await fetch('/attendance-stats');
        const data = await response.json();

        // Xử lý dữ liệu trả về để lấy danh sách tháng và giá trị ngày công
        const labels = data.map(item => item.month); // Trục X là danh sách tháng
        const validDays = data.map(item => item.valid_days); // Ngày công hợp lệ theo tháng
        const invalidDays = data.map(item => item.invalid_days); // Ngày công không hợp lệ theo tháng

        // Dữ liệu cho biểu đồ
        const chartData = {
            labels: labels, // Nhãn trục x
            datasets: [{
                    label: 'Ngày công hợp lệ',
                    data: validDays, // Dữ liệu cho ngày công hợp lệ
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Ngày công không hợp lệ',
                    data: invalidDays, // Dữ liệu cho ngày công không hợp lệ
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    fill: true,
                    tension: 0.4
                }
            ]
        };

        // Cấu hình biểu đồ
        const config = {
            type: 'line',
            data: chartData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Thống kê ngày công theo tháng'
                    }
                },
                interaction: {
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tháng'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số ngày công'
                        }
                    }
                }
            }
        };

        // Tạo biểu đồ mới
        new Chart(document.getElementById('attendanceChart'), config);
    }

    // Gọi hàm loadAttendanceStats khi trang được tải
    loadAttendanceStats();
</script>
