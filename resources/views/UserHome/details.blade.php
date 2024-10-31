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
        @if (session('success'))
            <div class="alert alert-success mt-2">{{ session('success') }}</div>
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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-person-badge"></i> Chi tiết
                                    người dùng</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Tên tài khoản:</strong> {{ $user->username }}</li>
                                    <li class="list-group-item"><strong>Họ và tên:</strong> {{ $user->name }}</li>
                                    <li class="list-group-item"><strong>Email:</strong> {{ $user->email }}</li>
                                    <li class="list-group-item"><strong>Phòng ban:</strong>
                                        {{ $user->department ? $user->department->name : 'N/A' }}</li>
                                    <li class="list-group-item"><strong>Số điện thoại:</strong> {{ $user->phone_number }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Nút Đổi Mật Khẩu và Modal -->
                    <div class="col-md-12">
                        <!-- Nút mở Modal -->
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#changePasswordModal">
                            Đổi Mật Khẩu
                        </button>
                    </div>
                </div>

                <!-- Modal Đổi Mật Khẩu -->
                <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="changePasswordModalLabel">Đổi Mật Khẩu</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('change.password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="form-control"
                                            placeholder="Nhập mật khẩu hiện tại..." required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                                        <input type="password" name="new_password" class="form-control"
                                            placeholder="Nhập mật khẩu mới..." required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password_confirmation" class="form-label">Xác nhận mật khẩu
                                            mới</label>
                                        <input type="password" name="new_password_confirmation" class="form-control"
                                            placeholder="Nhập lại mật khẩu mới..." required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Đổi Mật Khẩu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
