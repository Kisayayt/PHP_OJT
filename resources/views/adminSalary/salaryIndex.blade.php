@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <!-- Content: Salary Levels Table -->
            <div class="col-md-9">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <p style="color: red;">{{ $error }}</p>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Button Thêm Bậc Lương -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="{{ route('salaryLevels') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm bậc lương..."
                                value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </form>

                    <!-- Các nút được nhóm lại trong một div để tạo khoảng cách phù hợp -->
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary me-2"
                            onclick="window.location.href='/salaryLevels/create'">
                            <i class="bi bi-plus-circle"></i> Thêm Bậc Lương
                        </button>
                        <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal">
                            <i class="bi bi-x-lg"></i> Xóa mục đã chọn
                        </button>
                    </div>
                </div>


                <!-- Form xóa mềm nhiều mục -->
                <form id="deleteForm" action="{{ route('salaryLevels.softDeleteMultiple') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <!-- Bảng Salary Levels -->
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="select-all" />
                                        <label class="form-check-label" for="select-all"></label>
                                    </div>
                                </th> <!-- Checkbox column -->
                                <th>#</th>
                                <th>Tên Bậc Lương</th>
                                <th>Hệ Số Bậc Lương</th>
                                <th>Lương tháng</th>
                                <th>Cập nhật</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($salaryLevels as $salaryLevel)
                                <tr>
                                    <!-- Checkbox cho từng dòng -->
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $salaryLevel->id }}"
                                            class="form-check-input item-checkbox" />
                                    </td>
                                    <td>{{ $salaryLevel->id }}</td>
                                    <td>{{ $salaryLevel->level_name }}</td>
                                    <td>{{ $salaryLevel->salary_coefficient }}</td>
                                    <td>{{ number_format($salaryLevel->monthly_salary, 0, ',', '.') }} VND</td>
                                    <td>
                                        <a href="{{ route('salaryLevels.edit', $salaryLevel->id) }}"
                                            class="btn btn-success">
                                            <i class="bi bi-arrow-up-square"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center">
        {{ $salaryLevels->onEachSide(2)->appends(request()->input())->links() }}
    </div>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa các bậc lương đã chọn không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger" form="deleteForm">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script to select/deselect all checkboxes
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
@endsection
