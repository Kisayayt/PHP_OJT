@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <h1 style="font-weight: bold">Thêm người dùng</h1>
        <Button onclick="window.location.href='/dashboard'" class="btn btn-primary mb-3"><i class="bi bi-house"></i> Trở về
            trang chủ</Button>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <p style="color: red;">{{ $error }}</p>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="/insert" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="mb-3">
                        <label for="age" class="form-label">Tuổi</label>
                        <input type="text" class="form-control" id="age" name="age"
                            placeholder="Nhập tuổi của người dùng" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="employee_role" class="form-label">Vai trò nhân viên</label>
                        <select class="form-select" id="employee_role" name="employee_role" required>
                            <option value="">Chọn vai trò</option>
                            <option value="official">Chính thức</option>
                            <option value="part_time">Bán thời gian</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Nhập email"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Phòng ban</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Chọn phòng ban</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Chọn ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    </div>
                    <Button type="submit" class="btn btn-primary">Thêm người dùng <i class="bi bi-plus-square"></i>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên tài khoản</label>
                        <input type="text" class="form-control" id="username" name="username"
                            placeholder="Nhập username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Nhập password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation"
                            placeholder="Nhập lại password" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number"
                            placeholder="VD: +84 123456789" required>
                    </div>
                    <div class="mb-3">
                        <label for="salary_level" class="form-label">Bậc lương</label>
                        <select class="form-select" id="salary_level" name="salary_level">
                            <option value="">Chọn bậc lương</option>
                            @foreach ($salaryLevels as $salaryLevel)
                                <option value="{{ $salaryLevel->id }}">{{ $salaryLevel->level_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('keydown', function(event) {
            if (event.key === 'Backspace' && this.value === '+') {
                this.value = '';
            }
        });
    </script>


@endsection
