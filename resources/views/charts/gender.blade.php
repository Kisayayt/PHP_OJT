<h1>Bro what</h1>
<div>
    <canvas id="genderChart"></canvas>
    <canvas id="contractChart"></canvas>
    <canvas id="attendanceChart"></canvas>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const genderStats = @json($genderStats);
    const contractStats = @json($contractStats);
    const attendanceStats = @json($attendanceStats);

    // Biểu đồ giới tính
    const genderCtx = document.getElementById('genderChart').getContext('2d');
    new Chart(genderCtx, {
        type: 'bar',
        data: {
            labels: genderStats.map(stat => stat.department_name),
            datasets: [{
                    label: 'Male',
                    data: genderStats.map(stat => stat.male),
                    backgroundColor: 'blue'
                },
                {
                    label: 'Female',
                    data: genderStats.map(stat => stat.female),
                    backgroundColor: 'pink'
                },
                {
                    label: 'Other',
                    data: genderStats.map(stat => stat.other),
                    backgroundColor: 'gray'
                },
            ]
        }
    });

    // Biểu đồ loại hợp đồng và ngày công tương tự, xử lý thêm.
</script>
