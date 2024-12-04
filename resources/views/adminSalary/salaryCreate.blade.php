@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Thêm Bậc Lương</h2>
        <Button onclick="window.location.href='/salaryLevels'" class="btn btn-primary mb-3"><i class="bi bi-house"></i> Trở về
            trang chủ</Button>
        <form action="{{ route('salaryLevels.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Tên Bậc Lương</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="salary_coefficient" class="form-label">Hệ Số Bậc Lương</label>
                <input type="number" class="form-control" id="salary_coefficient" name="salary_coefficient" step="any"
                    required>
            </div>

            <div class="mb-3">
                <label for="monthly_salary" class="form-label">Lương tháng (VND)</label>
                <input type="text" class="form-control" id="monthly_salary" name="monthly_salary"
                    oninput="formatMoney(this)" required>
            </div>

            <button type="submit" class="btn btn-primary">Thêm Bậc Lương</button>
        </form>

    </div>
@endsection

{{-- <script>
    function formatMoney(input) {
        let value = input.value.replace(/\D/g, ''); // Xóa các ký tự không phải số
        value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.'); // Định dạng lại số
        input.value = value;
    }

    document.querySelector('form').addEventListener('submit', function(e) {
        let monthlySalaryInput = document.getElementById('monthly_salary');
        let value = monthlySalaryInput.value.replace(/\./g, ''); // Xóa dấu chấm trước khi gửi
        monthlySalaryInput.value = value;
    });
</script> --}}
