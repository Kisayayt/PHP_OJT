@extends('layouts.app')

@section('content')
    <div class="container pt-5">
        <h2 style="font-weight: bold">Sửa Bậc Lương</h2>
        <Button onclick="window.location.href='/salaryLevels'" class="btn btn-primary mb-3"><i class="bi bi-house"></i> Trở về
            trang chủ</Button>
        <form action="{{ route('salaryLevels.update', $salaryLevel->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên Bậc Lương</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $salaryLevel->level_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="salary_coefficient" class="form-label">Hệ Số Bậc Lương</label>
                <input type="number" class="form-control" id="salary_coefficient" name="salary_coefficient"
                    value="{{ old('salary_coefficient', $salaryLevel->salary_coefficient) }}" step="any" required>
            </div>

            <div class="mb-3">
                <label for="monthly_salary" class="form-label">Lương tháng (VND)</label>
                <input type="text" class="form-control" id="monthly_salary" name="monthly_salary"
                    value="{{ old('monthly_salary', $salaryLevel->monthly_salary) }}" oninput="formatMoney(this)" required>
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật Bậc Lương</button>
        </form>
    </div>
@endsection
