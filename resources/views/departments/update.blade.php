@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <h1 style="font-weight: bold">Cập nhật phòng ban</h1>
        <Button onclick="window.location.href='/departmentDashboard'" class="btn btn-primary mb-3"><i class="bi bi-house"></i>
            Back to
            home</Button>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <p style="color: red;">{{ $error }}></p>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="/insertDepartment" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên phòng ban: </label>
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder="Nhập tên phòng ban" required>
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Phòng ban cha</label>
                        <select class="form-select" id="parent_id" name="parent_id">
                            <option value="">Không có</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <Button type="submit" class="btn btn-primary">Thêm phòng ban <i class="bi bi-send-plus"></i></Button>


                </div>
            </div>

        </form>

    </div>
@endsection
