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
                        <button type="submit" class="btn btn-danger" form="bulkDeleteForm"><i class="bi bi-x-lg"></i> Xóa
                            được chọn</button>

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
                                            class="btn btn-success w-100 h-100"><i
                                                class="bi bi-arrow-up-square"></i></Button>
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

    <script>
        const selectAllCheckbox = document.getElementById('selectAll');

        const departmentCheckboxes = document.querySelectorAll('.user-checkbox');


        selectAllCheckbox.addEventListener('change', function() {

            const isChecked = selectAllCheckbox.checked;


            departmentCheckboxes.forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        });
    </script>
@endsection
