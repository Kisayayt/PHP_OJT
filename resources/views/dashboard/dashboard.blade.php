@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action active" aria-current="true">
                        <i class="bi bi-people"></i> Quản lí người dùng
                    </a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-door-closed"> </i>Quản lí
                        phòng ban</a>
                    <a href="#" class="list-group-item list-group-item-action"><i class="bi bi-person-check-fill"></i>
                        Quản lí checkout</a>
                </div>
            </div>
            <div class="col-md-9">
                <Button type="button" class="btn btn-primary" onclick="window.location.href='/create'"> <i
                        class="bi bi-person-plus"></i>
                    CREATE
                    USER</Button>
                <table class="table mt-3 mb-5">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">name</th>
                            <th scope="col">email</th>
                            <th scope="col">phone number</th>
                            <th scope="col">Phòng ban</th>
                            <th scope="col">Update</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->department ? $user->department->name : 'N/A' }}</td>
                                <td><Button onclick="window.location.href='/update/{{ $user->id }}'" type="button"
                                        class="btn btn-secondary"><i class="bi bi-arrow-up-square"></i> Update</Button></td>
                                <td><Button type="button" class="btn btn-danger"><i class="bi bi-person-x"></i>
                                        Delete</Button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $users->links() }} <!-- Đây sẽ tạo các link phân trang -->
                </div>
            </div>
        </div>
    </div>
@endsection
