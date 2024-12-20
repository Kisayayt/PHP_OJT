@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <h1 style="font-weight: bold">Cập nhật người dùng</h1>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <p style="color: red;">{{ $error }}</p>
                    @endforeach
                </ul>
            </div>
        @endif
        <Button onclick="window.location.href='/dashboard'" class="btn btn-primary mb-3"><i class="bi bi-house"></i> Trở về
            trang chủ</Button>
        <form action="/updated/{{ $user->id }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input value="{{ $user->name }}" type="text" class="form-control" id="name" name="name"
                            placeholder="Nhập họ và tên" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input value="{{ $user->email }}" type="text" class="form-control" id="email" name="email"
                            placeholder="Nhập email" required>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Phòng ban</label>
                        {{-- <option value="">Select Department</option> --}}

                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">Select Department</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}"
                                    {{ $department->id == $user->department_id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>


                    <div class="mb-3">
                        <label for="avatar" class="form-label">Chọn ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                    </div>
                    <!-- Vai trò nhân viên -->
                    <div class="mb-3">
                        <label for="employee_role" class="form-label">Vai trò nhân viên</label>
                        <select class="form-select" id="employee_role" name="employee_role" required>
                            <option value="">Chọn vai trò</option>
                            <option value="official" {{ $user->employee_role === 'official' ? 'selected' : '' }}>Chính
                                thức</option>
                            <option value="part_time" {{ $user->employee_role === 'part_time' ? 'selected' : '' }}>Bán
                                thời gian</option>
                        </select>
                    </div>

                    <Button type="submit" class="btn btn-primary">Cập nhật <i class="bi bi-send-plus"></i></Button>
                    <Button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#passwordModal">
                        Đổi mật khẩu
                    </Button>


                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="username" class="form-label">Tên tài khoản</label>
                        <input value="{{ $user->username }}" type="text" class="form-control" id="username"
                            name="username" placeholder="Nhập username" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Số điện thoại</label>
                        <input value="{{ $user->phone_number }}" type="text" class="form-control" id="phone_number"
                            name="phone_number" placeholder="Nhập sđt" required>
                    </div>
                    <div class="col-md-12">
                        <!-- Tuổi -->
                        <div class="mb-3">
                            <label for="age" class="form-label">Tuổi</label>
                            <input value="{{ $user->age }}" type="number" class="form-control" id="age"
                                name="age" placeholder="Nhập tuổi" required>
                        </div>

                        <!-- Giới tính -->
                        <div class="mb-3">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Chọn giới tính</option>
                                <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>


                    </div>

                    <div class="mb-3">
                        <label for="salary_level" class="form-label">Cấp bậc lương</label>
                        <select class="form-select" id="salary_level" name="salary_level">
                            <option value="">Chọn cấp bậc lương</option>
                            @foreach ($salaryLevels as $salaryLevel)
                                <option value="{{ $salaryLevel->id }}"
                                    {{ $currentSalaryLevel && $salaryLevel->id == $currentSalaryLevel->id ? 'selected' : '' }}>
                                    {{ $salaryLevel->level_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>
            </div>
        </form>
        <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="passwordForm" action="{{ route('user.updatePassword', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="passwordModalLabel">Đổi mật khẩu</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection


<script>
    const phoneInput = document.getElementById('phone_number');

    phoneInput.addEventListener('keydown', function(event) {
        if (event.key === 'Backspace' && this.value === '+') {
            this.value = '';
        }
    });
</script>
