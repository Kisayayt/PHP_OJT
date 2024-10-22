@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex">
                        <form action="/departmentDashboard/search" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm phòng ban..." value="{{ request('search') }}"
                                    style="max-width: 200px;">
                                <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <Button type="button" class="btn btn-primary" onclick="window.location.href='/createDepartment'">
                            <i class="bi bi-plus-square"></i> Thêm phòng ban
                        </Button>
                        <button type="submit" class="btn btn-danger" form="bulkDeleteForm">
                            <i class="bi bi-x-lg"></i> Xóa được chọn
                        </button>
                    </div>
                </div>

                <form action="{{ route('departments.bulkDelete') }}" method="post" id="bulkDeleteForm">
                    @csrf
                    @method('delete')

                    <table class="table mt-3 mb-5">
                        <thead>
                            <tr>
                                <th scope="col"><input type="checkbox" id="selectAll"></th>
                                <th scope="col">#</th>
                                <th scope="col">Tên</th>
                                <th scope="col">Phòng ban cha</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Cập nhật</th>
                                <th scope="col">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td><input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            class="department-checkbox"></td>
                                    <td>{{ $department->id }}</td>
                                    <td><strong>{{ $department->name }}</strong></td>
                                    <td>{{ $department->parent ? $department->parent->name : 'Không có' }}</td>
                                    <td>
                                        <Button
                                            onclick="window.location.href='/departments/{{ $department->id }}/update-status'"
                                            type="button"
                                            class="btn {{ $department->status ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $department->status ? 'Hoạt động' : 'Đình chỉ' }}
                                        </Button>
                                    </td>
                                    <td>
                                        <Button onclick="window.location.href='/updateDepartment/{{ $department->id }}'"
                                            type="button" class="btn btn-success w-100">
                                            <i class="bi bi-arrow-up-square"></i>
                                        </Button>
                                    </td>
                                    <td>
                                        <Button
                                            onclick="window.location.href='/departmentDashboard/{{ $department->id }}/details'"
                                            type="button" class="btn btn-info w-100">
                                            <i class="bi bi-info-circle"></i>
                                        </Button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>

                <div class="d-flex justify-content-center">
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Lấy checkbox chính
        const selectAllCheckbox = document.getElementById('selectAll');
        // Lấy tất cả các checkbox trong tbody
        const departmentCheckboxes = document.querySelectorAll('.department-checkbox');

        // Khi checkbox chính được click
        selectAllCheckbox.addEventListener('change', function() {
            // Kiểm tra xem checkbox chính có được chọn hay không
            const isChecked = selectAllCheckbox.checked;

            // Lặp qua tất cả các checkbox con và set trạng thái tương tự
            departmentCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
    </script>
@endsection
