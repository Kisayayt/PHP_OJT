@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
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
                        <button type="button" class="btn btn-primary" onclick="window.location.href='/createDepartment'">
                            <i class="bi bi-plus-square"></i> Thêm phòng ban
                        </button>
                        <button type="submit" class="btn btn-danger" form="bulkDeleteForm">
                            <i class="bi bi-x-lg"></i> Xóa được chọn
                        </button>
                        <div class="dropdown d-inline-block ml-2">
                            <button type="button" class="btn btn-success"
                                onclick="window.location.href='/departmentDashboard/export-excel-all'">
                                Xuất file</button>
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Nhập file
                            </button>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <form action="{{ route('exportDepartment') }}" method="GET">
                                        <button type="submit" class="dropdown-item">Xuất file mẫu</button>
                                    </form>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item"
                                        onclick="document.getElementById('import_file_input').click();">
                                        Nhập file
                                    </button>
                                    <form id="import_file" action="{{ route('importDepartment') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="file" id="import_file_input" name="import_file" class="d-none"
                                            required onchange="this.form.submit()">
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form action="{{ route('departments.bulkDelete') }}" method="post" id="bulkDeleteForm">
                    @csrf
                    @method('delete')
                    <!-- Accordion danh sách phòng ban -->
                    <div class="accordion mt-3" id="departmentAccordion">
                        @foreach ($departments as $department)
                            <!-- Phòng ban cha -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $department->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $department->id }}" aria-expanded="false"
                                        aria-controls="collapse{{ $department->id }}">
                                        <input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            class="me-2 department-checkbox">
                                        {{ $department->name }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $department->id }}" class="accordion-collapse collapse"
                                    aria-labelledby="heading{{ $department->id }}" data-bs-parent="#departmentAccordion">
                                    <div class="accordion-body">
                                        @include('departments.children', [
                                            'children' => $department->children,
                                        ])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>

                <div class="d-flex justify-content-center mt-3">
                    {{ $departments->onEachSide(2)->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Checkbox chính để chọn tất cả
        const selectAllCheckbox = document.getElementById('selectAll');
        const departmentCheckboxes = document.querySelectorAll('.department-checkbox');

        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = selectAllCheckbox.checked;
            departmentCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
    </script>
@endsection
