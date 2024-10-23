@extends('userLayouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <div>
            <h2 style="font-weight: bold"><i class="bi bi-house"></i> Trang chủ</h2>
            @if ($time > 0)
                <p>Tổng thời gian gần đây nhất: <strong>{{ $time }} giờ</strong></p>
            @else
                <p>Tổng thời gian gần đây nhất: <strong>0 giờ</strong></p>
            @endif
        </div>
        <div class="row">

            <div class="col-md-3">
                <div class="card text-center">
                    <img src="{{ asset(auth()->user()->avatar) }}" class="card-img-top" alt="User Image" width="300"
                        height="300" style="object-fit: cover">
                    <div class="card-body">
                        {{-- @dd(auth()->user()); --}}
                        <h5 style="font-weight: bold" class="card-title">{{ auth()->user()->name }}</h5>
                        <p class="card-text">{{ auth()->user()->department->name }}</p>
                        <div>
                            <a href="#" class="btn btn-primary btn-block mt-3 w-100">Thông tin người dùng</a>
                            <a href="#history-table" class="btn btn-secondary btn-block mt-2 w-100">Lịch sử
                                check-in/check-out</a>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf <!-- Thêm token CSRF -->
                                <button type="submit" class="btn btn-danger btn-block mt-2 w-100">Đăng xuất</button>
                            </form>

                        </div>
                    </div>
                </div>
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

                <!-- Table lịch sử check-in/check-out -->
                <div id="history-table" class="mt-4">
                    <h4>Lịch sử check-in/check-out</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Ngày/tháng/năm</th>
                                <th>Loại</th>
                                <th>Thời lượng</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- @dd($history); --}}
                            @foreach ($history as $record)
                                <tr>
                                    <td>{{ $record->created_at->format('H:i') }}</td>
                                    <td>{{ $record->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $record->type == 'in' ? 'Check-in' : 'Check-out' }}</td>
                                    <td>
                                        @if ($record->type == 'out')
                                            {{ $record->time }} tiếng
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
