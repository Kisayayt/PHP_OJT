@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <div>
            <h2 style="font-weight: bold"><i class="bi bi-house"></i> Chi tiết</h2>
        </div>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <div class="col-md-9">
                <Button onclick="window.location.href='/dashboard'" class="btn btn-primary mb-3"><i class="bi bi-house"></i>
                    Trở về
                    trang chủ</Button>
                <div style="background-color: rgb(243, 243, 243); border-radius: 2%" class="row p-3 m-1">
                    <!-- Cột chứa ảnh avatar -->
                    <div class="col-md-3 d-flex justify-content-center align-items-center">
                        <img src="{{ asset($user->avatar ? $user->avatar : 'images/default-avatar.png') }}" alt="Avatar"
                            class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
                    </div>

                    <!-- Cột chứa thông tin chi tiết và trạng thái check-in -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header">
                                <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-person-badge"></i> Chi tiết
                                    người dùng</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <strong>Tên tài khoản:</strong> {{ $user->username }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Họ và tên:</strong> {{ $user->name }}
                                    </li>

                                    <li class="list-group-item">
                                        <strong>Bậc lương:</strong>
                                        {{ $user->salaryLevel ? $user->salaryLevel->level_name : 'N/A' }}
                                    </li>


                                    <li class="list-group-item">
                                        <strong>Email:</strong> {{ $user->email }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Phòng ban:</strong>
                                        {{ $user->department ? $user->department->name : 'N/A' }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Số điện thoại:</strong> {{ $user->phone_number }}
                                    </li>
                                    <li class="list-group-item">
                                        <strong>Trạng thái Check-in:</strong>
                                        @if ($user->isCheckedIn)
                                            <span class="text-success"> Đang check-in
                                                {{ $lastCheckIn ? '(' . $lastCheckIn->created_at->format('d/m/Y H:i:s') . ')' : '' }}
                                            </span>
                                        @else
                                            <span class="text-danger"> Đã check-out
                                                {{ $lastCheckOut ? '(' . $lastCheckOut->created_at->format('d/m/Y H:i:s') . ')' : '' }}
                                            </span>
                                        @endif
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
