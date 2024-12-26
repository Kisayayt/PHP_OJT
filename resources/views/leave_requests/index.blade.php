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
                                <td>
                                    @php
                                        $leaveTypeMapping = [
                                            'morning' => 'Buổi sáng',
                                            'afternoon' => 'Buổi chiều',
                                            'full_day' => 'Cả ngày',
                                            'multiple_days' => 'Nhiều ngày',
                                        ];
                                    @endphp
                                    {{ $leaveTypeMapping[$leave->leave_type] ?? 'Không xác định' }}
                                </td>

                                <td>{{ $leave->start_date }}</td>
                                <td>{{ $leave->end_date }}</td>
                                <td>{{ $leave->reason }}</td>
                                <td>
                                    @if ($leave->status == 0)
                                        <span class="badge bg-warning text-dark">Đang chờ duyệt</span>
                                    @elseif ($leave->status == 1)
                                        <span class="badge bg-success text-white">Đã duyệt</span>
                                    @elseif ($leave->status == 2)
                                        <span class="badge bg-danger text-white">Từ chối</span>
                                    @else
                                        <span class="badge bg-secondary text-white">Không xác định</span>
                                    @endif
                                </td>


                                <td>
                                    @if ($leave->status == 0)
                                        <a href="{{ route('leave_requests.edit', $leave->id) }}"
                                            class="btn btn-warning">Sửa</a>
                                        <form action="{{ route('leave_requests.destroy', $leave->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger"
                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</button>
                                        </form>
                                    @else
                                        <span class="text-muted">----</span>
                                    @endif
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
