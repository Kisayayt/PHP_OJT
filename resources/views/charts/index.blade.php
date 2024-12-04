@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <canvas id="employeeRatioChart" width="400" height="300"></canvas>
                <canvas id="ageGenderChart" width="400" height="300"></canvas>
            </div>
        </div>
    </div>
@endsection

<style>
    #employeeRatioChart,
    #ageGenderChart {
        max-width: 600px;
        max-height: 400px;
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
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    borderColor: '#000',
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

    loadEmployeeRatioChart();
    loadAgeGenderChart();
</script>
