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
                    <button type="submit" class="btn btn-danger" id="selectAllButton"><i class="bi bi-x-lg"></i> Xóa được
                        chọn</button>
                    <table class="table mt-3 mb-5">
                        <thead>
                            <tr>
                                <th scope="col"><input type="checkbox" id="selectAll"></th>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Phòng ban cha</th>
                                <th scope="col">Status</th>
                                <th scope="col">Update</th>
                                <th scope="col">Details</th>
                                {{-- <th scope="col">Delete</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr>
                                    <td><input type="checkbox" name="department_ids[]" value="{{ $department->id }}"
                                            class="user-checkbox"></td>
                                    <td>{{ $department->id }}</td>
                                    <td><Strong>{{ $department->name }}</Strong></td>
                                    <td>{{ $department->parent ? $department->parent->name : 'Không có' }}</td>
                                    <td>
                                        {{-- <form action="{{ route('departments.updateStatus', $department->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn w-100 {{ $department->status ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $department->status ? 'active' : 'inactive' }}
                                            </button>
                                        </form> --}}
                                        <Button
                                            onclick="window.location.href='/departments/{{ $department->id }}/update-status'"
                                            type="button"
                                            class="btn {{ $department->status ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $department->status ? 'active' : 'inactive' }}
                                        </Button>
                                    </td>
                                    <td><Button onclick="window.location.href='/updateDepartment/{{ $department->id }}'"
                                            type="button" class="btn btn-secondary"><i class="bi bi-arrow-up-square"></i>
                                            Update</Button>
                                    </td>

                                    <td>
                                        <Button
                                            onclick="window.location.href='/departmentDashboard/{{ $department->id }}/details'"
                                            type="button" class="btn btn-info"><i class="bi bi-info-circle"></i>Details
                                        </Button>
                                    </td>


                                    {{-- <form action="/deleteDepartment/{{ $department->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <td><Button type="submit" class="btn btn-danger"><i class="bi bi-door-closed"></i>
                                                Delete</Button>
                                        </td>
                                    </form> --}}

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
