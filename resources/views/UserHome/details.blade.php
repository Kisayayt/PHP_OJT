@extends('userLayouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <div>
            <h2 style="font-weight: bold"><i class="bi bi-house"></i> Chi tiết</h2>
            @if ($lastCheckoutTime > 0)
                <p>Tổng thời gian gần đây nhất: <strong>{{ $lastCheckoutTime }} giờ</strong></p>
            @else
                <p>Tổng thời gian gần đây nhất: <strong>0 giờ</strong></p>
            @endif
        </div>
        <div class="row">

            <div class="col-md-3">
                @include('Userhome.card')
            </div>


            <div class="col-md-9">
                @if ($isCheckedIn)
                    <div class="alert alert-success">
                        <p>Bạn đã check-in!</p>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Check Out</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <p>Bạn chưa check-in!</p>
                        <form action="{{ route('checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Check In</button>
                        </form>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-person-badge"></i> Chi tiết
                                    người
                                    dùng
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">

                                    <li class="list-group-item">
                                        <strong>Họ và tên:</strong> {{ $user->name }}
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
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <div class="card">
                                <div class="card-header">
                                    <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-file-lock2-fill"></i> Đổi
                                        mật
                                        khẩu
                                    </h5>

                                </div>
                                <div class="card-body">
                                    <form action=" {{ route('change.password') }}" method="POST">
                                        @csrf
                                        <li class="list-group-item">
                                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                            <input type="password" name="current_password" class="form-control"
                                                placeholder="Nhập mật khẩu hiện tại..." required>
                                        </li>
                                        <li class="list-group-item">
                                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                                            <input type="password" name="new_password" class="form-control"
                                                placeholder="Nhập mật mới..." required>
                                        </li>
                                        <li class="list-group-item">
                                            <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu
                                                mới</label>
                                            <input type="password" name="new_password_confirmation"
                                                placeholder="Nhập lại mật khẩu mới..." class="form-control" required>
                                        </li>
                                        <li class="list-group-item">
                                            <button type="submit" class="btn btn-primary mt-5">Đổi Mật Khẩu</button>
                                        </li>
                                        @if (session('success'))
                                            <p>{{ session('success') }}</p>
                                        @endif
                                    </form>
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>




            </div>
        </div>
    </div>
@endsection
