@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <h1 style="font-weight: bold">Cập nhật phòng ban</h1>
        <Button onclick="window.location.href='/departmentDashboard'" class="btn btn-primary mb-3"><i class="bi bi-house"></i>
            Trở về trang chủ</Button>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <p style="color: red;">{{ $error }}</p>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/updatedDepartment/{{ $department->id }}" method="post">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phòng ban: </label>
                        <input value="{{ $department->name }}" type="text" class="form-control" id="name"
                            name="name" placeholder="Nhập tên phòng ban" required>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Phòng ban cha</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Không có</option>
                            @foreach ($departments as $departmentFor)
                                @if ($departmentFor->status == 1)
                                    <option value="{{ $departmentFor->id }}"
                                        {{ $departmentFor->id == $department->parent_id ? 'selected' : '' }}>
                                        {{ $departmentFor->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <Button type="submit" class="btn btn-primary">Lưu <i class="bi bi-send-plus"></i></Button>


                </div>
            </div>

        </form>

    </div>
@endsection
