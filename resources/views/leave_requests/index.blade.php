@extends('userlayouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold"><i class="bi bi-house"></i> Các đơn nghỉ phép</h2>
        @if ($lastCheckoutTime)
            @php
                $hour = floor($lastCheckoutTime / 60);
                $minute = $lastCheckoutTime % 60;
            @endphp
            <p>Thời gian gần đây nhất: <strong>{{ $hour }} giờ {{ $minute }} phút</strong></p>
        @else
            <p>Thời gian gần đây nhất: <strong>0 giờ</strong></p>
        @endif
        <div class="row">
            <div class="col-md-3">
                @include('userHome.card')
            </div>

            <div class="col-md-9">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Loại nghỉ</th>
                            <th>Ngày bắt đầu</th>
                            <th>Ngày kết thúc</th>
                            <th>Lý do</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveRequests as $leave)
                            <tr>
                                <td>{{ $leave->leave_type }}</td>
                                <td>{{ $leave->start_date }}</td>
                                <td>{{ $leave->end_date }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>{{ $leave->status == 0 ? 'Đang chờ duyệt' : 'Đã duyệt' }}</td>
                                <td>
                                    <a href="{{ route('leave_requests.edit', $leave->id) }}" class="btn btn-warning">Sửa</a>
                                    <form action="{{ route('leave_requests.destroy', $leave->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger"
                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    {{ $leaveRequests->onEachSide(2)->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
