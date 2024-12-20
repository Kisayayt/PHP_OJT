<div class="col-md-12">
    <h4 class="text-center mb-3">Số lượng nhân viên theo loại hợp đồng trong từng phòng ban</h4>
    <select id="departmentDropdown" class="form-select mb-3" onchange="updateContractTypeChart()">
        <option value="">Tất cả phòng ban</option>
    </select>
    <!-- Điều chỉnh kích thước canvas bằng CSS -->
    <canvas id="contractTypeChart" style="width: 100%; height: 300px;"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    let contractTypeChart;

    async function loadDepartments() {
        const response = await fetch('/api/contract-type-by-department');
        const data = await response.json();

        const departmentDropdown = document.getElementById('departmentDropdown');
        const departments = [...new Set(data.map(item => item.department_name))];

        // Thêm các phòng ban vào dropdown
        departments.forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            departmentDropdown.appendChild(option);
        });
    }

    async function loadContractTypeChart(department = '') {
        const response = await fetch('/api/contract-type-by-department');
        const data = await response.json();

        // Lọc dữ liệu theo phòng ban nếu được chọn
        const filteredData = department ?
            data.filter(item => item.department_name === department) :
            data;

        // Tổng hợp dữ liệu cho "Official" và "Part-time"
        const officialCount = filteredData
            .filter(item => item.employee_role === 'official')
            .reduce((sum, item) => sum + item.total, 0);

        const partTimeCount = filteredData
            .filter(item => item.employee_role === 'part_time')
            .reduce((sum, item) => sum + item.total, 0);

        // Nếu đã có biểu đồ, hủy biểu đồ cũ trước khi tạo biểu đồ mới
        if (contractTypeChart) {
            contractTypeChart.destroy();
        }

        // Tạo biểu đồ mới
        contractTypeChart = new Chart(document.getElementById('contractTypeChart'), {
            type: 'pie', // Biểu đồ tròn
            data: {
                labels: ['Chính thức (Official)', 'Bán thời gian (Part-time)'], // Nhãn loại hợp đồng
                datasets: [{
                    data: [officialCount,
                        partTimeCount
                    ], // Tổng số nhân viên theo từng loại hợp đồng
                    backgroundColor: ['#36A2EB', '#FF6384'], // Màu sắc
                    hoverBackgroundColor: ['#2F8DCB', '#E75A70'], // Màu khi hover
                }, ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true, // Duy trì tỷ lệ
                aspectRatio: 2, // Tùy chỉnh tỷ lệ chiều rộng / chiều cao
                plugins: {
                    legend: {
                        display: true,
                        position: 'right', // Chú thích ở bên phải
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                return `${label}: ${value} nhân viên`;
                            },
                        },
                    },
                },
            },
        });
    }

    // Hàm gọi khi người dùng chọn phòng ban
    function updateContractTypeChart() {
        const department = document.getElementById('departmentDropdown').value;
        loadContractTypeChart(department);
    }

    // Khởi tạo
    loadDepartments();
    loadContractTypeChart();
</script>
