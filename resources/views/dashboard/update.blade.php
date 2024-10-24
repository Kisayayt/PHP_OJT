@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">

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
                        <label for="department_id" class="form-label">Departments</label>
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
                    <Button type="submit" class="btn btn-primary">Submit <i class="bi bi-send-plus"></i></Button>


                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input value="" type="password" class="form-control" id="password" name="password"
                            placeholder="Nhập password mới">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input value="" type="password" class="form-control" name="password_confirmation"
                            id="password_confirmation" placeholder="Nhập lại password mới">
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone number</label>
                        <input value="{{ $user->phone_number }}" type="text" class="form-control" id="phone_number"
                            name="phone_number" placeholder="Nhập sđt" required>
                    </div>

                </div>
            </div>

        </form>

    </div>
    <script>
        const phoneInput = document.getElementById('phone_number');

        phoneInput.addEventListener('input', function() {

            if (this.value === '' || this.value === '+84') {
                return;
            }


            if (!this.value.startsWith('+84')) {

                this.value = '+84 ' + this.value.replace(/^0/, '');
            }
        });

        phoneInput.addEventListener('keydown', function(event) {

            if (event.key === 'Backspace' && this.value === '+84 ') {
                this.value = '';
            }
        });
    </script>


@endsection
