@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        </h2>
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
                        <form action="/dashboard/search" method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Tìm kiếm người dùng..." value="{{ request('search') }}"
                                    style="max-width: 200px;">
                                <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                            </div>
                        </form>
                    </div>
                    <div>
                        <Button type="button" class="btn btn-primary" onclick="window.location.href='/create'">
                            <i class="bi bi-person-plus"></i> Thêm người dùng
                        </Button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal">
                            <i class="bi bi-x-lg"></i> Xóa được chọn
                        </button>

                        <button type="submit" class="btn btn-success" onclick="window.location.href='/export-excel-all'">
                            Xuất
                            file</button>

                        <div class="dropdown d-inline-block ml-2">
                            <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Nhập file
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <form action="{{ route('export') }}" method="GET">
                                        <button type="submit" class="dropdown-item">Xuất file mẫu</button>
                                    </form>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item"
                                        onclick="document.getElementById('import_file_input').click();">
                                        Nhập file
                                    </button>
                                    <form id="import_file" action="{{ route('import') }}" method="POST"
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




                <table class="table mt-3 mb-5 table-striped">
                    <thead>
                        <tr>
                            <th scope="col"><input type="checkbox" id="selectAll"></th>
                            <th scope="col">#</th>
                            <th scope="col">Ảnh</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Email</th>
                            <th scope="col">SĐT</th>
                            <th scope="col">Phòng ban</th>
                            <th scope="col">Cập nhật</th>
                            <th scope="col">Chi tiết</th>
                            <th scope="col">Check-in/out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <form action="{{ route('users.bulkDelete') }}" method="post" id="bulkDeleteForm">
                            @csrf
                            @method('delete')
                            @foreach ($users as $user)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                            class="user-checkbox"></td>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <img src="{{ asset($user->avatar) }}" alt="{{ $user->name }}" width="50"
                                            height="50" style="object-fit: cover;">
                                    </td>
                                    <td><strong>{{ $user->name }}</strong></td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>
                                    <td>
                                        <Button onclick="window.location.href='/update/{{ $user->id }}'" type="button"
                                            class="btn btn-success w-100 h-100">
                                            <i class="bi bi-arrow-up-square"></i></Button>
                                    </td>
                                    <td>
                                        <Button onclick="window.location.href='/dashboard/{{ $user->id }}/details'"
                                            type="button" class="btn btn-info w-100 h-100">
                                            <i class="bi bi-info-circle"></i>
                                        </Button>
                                    </td>
                                    <td>
                                        @if ($user->isCheckedIn)
                                            <Button type="button" class="btn btn-success w-100 h-100" disabled>
                                                <i class="bi bi-door-closed-fill"></i>
                                            </Button>
                                        @else
                                            <Button type="button" class="btn btn-danger w-100 h-100" disabled>
                                                <i class="bi bi-door-open-fill"></i>
                                            </Button>
                                        @endif
                                    </td>

                                </tr>
                            @endforeach

                        </form>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $users->onEachSide(2)->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa các người dùng đã chọn không?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger" form="bulkDeleteForm">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <canvas id="employeeRatioChart" width="400" height="300"></canvas>


    <script>
        const selectAllCheckbox = document.getElementById('selectAll');

        const departmentCheckboxes = document.querySelectorAll('.user-checkbox');


        selectAllCheckbox.addEventListener('change', function() {

            const isChecked = selectAllCheckbox.checked;


            departmentCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });



        async function loadEmployeeRatioChart() {
            const response = await fetch('/api/employee-ratio-by-department');
            const data = await response.json();

            new Chart(document.getElementById('employeeRatioChart'), {
                type: 'bar', // Chuyển sang biểu đồ cột
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Tỉ lệ nhân sự',
                        data: data.counts,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                        borderColor: '#000',
                        borderWidth: 1,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Không giữ tỉ lệ để tùy chỉnh kích thước
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Phòng ban',
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Số lượng nhân sự',
                            }
                        }
                    }
                }
            });
        }
        loadEmployeeRatioChart();
    </script>
@endsection

<style>
    /* Giảm kích thước canvas bằng CSS */
    #employeeRatioChart {
        max-width: 600px;
        max-height: 400px;
        margin: 0 auto;
        /* Căn giữa biểu đồ */
    }
</style>
