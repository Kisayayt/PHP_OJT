@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <canvas id="employeeRatioChart"></canvas>
                    </div>
                    <div class="col-md-6 mb-4">
                        <canvas id="ageGenderChart"></canvas>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <canvas id="contractTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    #employeeRatioChart,
    #contractTypeChart,
    #ageGenderChart {
        max-width: 100%;
        height: 400px;
        /* Giới hạn chiều cao */
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 20px;
        margin-top: 20px;
    }
</style>


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

    async function loadAgeGenderChart() {
        const response = await fetch('/api/age-gender-stats-by-department');
        const data = await response.json();

        const labels = [...new Set(data.map(item => item.department_name))];
        const ageGroups = ['18-30', '31-45', '46+'];
        const genders = ['male', 'female'];

        const datasets = [];

        // Tạo các dataset cho mỗi nhóm tuổi và giới tính
        ageGroups.forEach((group, index) => {
            genders.forEach(gender => {
                const label = `Nhóm tuổi: ${group} - Giới tính: ${gender}`;
                const color = index === 0 ? '#FF6384' : (index === 1 ? '#36A2EB' : '#FFCE56');

                datasets.push({
                    label: label,
                    data: labels.map(label => {
                        const filtered = data.find(item => item.department_name ===
                            label && item.age_group === group && item.gender ===
                            gender);
                        return filtered ? filtered.total : 0;
                    }),
                    backgroundColor: color,
                    borderColor: '#000',
                    borderWidth: 1
                });
            });
        });

        new Chart(document.getElementById('ageGenderChart'), {
            type: 'bar',
            data: {
                labels,
                datasets
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
                            text: 'Số lượng nhân sự theo nhóm tuổi và giới tính',
                        }
                    }
                }
            }
        });
    }

    async function loadContractTypeChart() {
        const response = await fetch('/api/contract-type-by-department');
        const data = await response.json();

        const departments = [...new Set(data.map(item => item.department_name))];
        const officialCounts = departments.map(dept => {
            const entry = data.find(item => item.department_name === dept && item.employee_role ===
                'official');
            return entry ? entry.total : 0;
        });
        const partTimeCounts = departments.map(dept => {
            const entry = data.find(item => item.department_name === dept && item.employee_role ===
                'part_time');
            return entry ? entry.total : 0;
        });

        new Chart(document.getElementById('contractTypeChart'), {
            type: 'bar', // Biểu đồ dạng cột ngang
            data: {
                labels: departments,
                datasets: [{
                        label: 'Chính thức (Official)',
                        data: officialCounts,
                        backgroundColor: '#36A2EB',
                        borderColor: '#000',
                        borderWidth: 1,
                    },
                    {
                        label: 'Bán thời gian (Part-time)',
                        data: partTimeCounts,
                        backgroundColor: '#FF6384',
                        borderColor: '#000',
                        borderWidth: 1,
                    }
                ]
            },
            options: {
                indexAxis: 'y', // Chuyển trục X và Y
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
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Số lượng nhân sự',
                        },
                        ticks: {
                            stepSize: 1,
                            precision: 0, // Không hiển thị số thập phân
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Phòng ban',
                        },
                        ticks: {
                            autoSkip: false,
                        }
                    }
                }
            }
        });
    }


    loadContractTypeChart();
    loadEmployeeRatioChart();
    loadAgeGenderChart();
</script>
