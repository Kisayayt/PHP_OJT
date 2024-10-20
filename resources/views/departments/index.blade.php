@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            {{-- Table người dùng --}}

            <div class="col-md-9">
                <Button style="margin-bottom: 10px" type="button" class="btn btn-primary"
                    onclick="window.location.href='/createDepartment'"><i class="bi bi-plus-square"></i> Thêm phòng
                    ban</Button>

                <form action="{{ route('departments.bulkDelete') }}" method="post" id="bulkDeleteForm">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger" id="selectAllButton">Xóa được chọn</button>
                    <table class="table mt-3 mb-5">
                        <thead>
                            <tr>
                                <th scope="col"><input type="checkbox" id="selectAll"></th>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Phòng ban cha</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update</th>
                                <th scope="col">Delete</th>
                                <th scope="col">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td><input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            class="user-checkbox"></td>
                                    <td>{{ $department->id }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->parent ? $department->parent->name : 'Không có' }}</td>
                                    <td>{{ $department->status ? 'Hoạt động' : 'Không hoạt động' }}</td>
                                    <td><Button onclick="window.location.href='/updateDepartment/{{ $department->id }}'"
                                            type="button" class="btn btn-secondary"><i class="bi bi-arrow-up-square"></i>
                                            Update</Button>
                                    </td>

                                    <form action="/deleteDepartment/{{ $department->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <td><Button type="submit" class="btn btn-danger"><i class="bi bi-door-closed"></i>
                                                Delete</Button>
                                        </td>
                                    </form>
                                    <form action="/departmentDashboard/{{ $department->id }}/details" method="get">

                                        <td><Button type="submit" class="btn btn-info"><i class="bi bi-info-circle"></i>
                                                Details</Button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
                <div class="d-flex justify-content-center">
                    {{ $departments->links() }} <!-- Đây sẽ tạo các link phân trang -->
                </div>
            </div>
        </div>
    </div>
@endsection