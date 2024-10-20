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
                    onclick="window.location.href='/create'"> <i class="bi bi-person-plus"></i> Thêm người dùng</Button>

                <form action="{{ route('users.bulkDelete') }}" method="post" id="bulkDeleteForm">
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
                                <th scope="col">Email</th>
                                <th scope="col">Phone number</th>
                                <th scope="col">Phòng ban</th>
                                <th scope="col">Update</th>
                                <th scope="col">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                            class="user-checkbox"></td>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>
                                    <td>
                                        <Button onclick="window.location.href='/update/{{ $user->id }}'" type="button"
                                            class="btn btn-secondary"><i class="bi bi-arrow-up-square"></i> Update</Button>
                                    </td>

                                    <form action="/deleteUser/{{ $user->id }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <td><Button type="button" class="btn btn-danger"><i class="bi bi-person-x"></i>
                                                Delete</Button>
                                        </td>
                                    </form>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
                <div class="d-flex justify-content-center">
                    {{ $users->links() }} <!-- Đây sẽ tạo các link phân trang -->
                </div>
            </div>
        </div>
    </div>
@endsection
