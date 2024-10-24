@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        </h2>
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
                                    {{-- @dd($user->avatar); --}}
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
                                            class="btn btn-success w-100"><i class="bi bi-arrow-up-square"></i>
                                        </Button>
                                    </td>
                                </tr>
                            @endforeach
                        </form>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Lấy checkbox chính
        const selectAllCheckbox = document.getElementById('selectAll');
        // Lấy tất cả các checkbox trong tbody
        const departmentCheckboxes = document.querySelectorAll('.user-checkbox');

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
