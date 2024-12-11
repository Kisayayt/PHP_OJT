@extends('layouts.app')

@section('content')
    <div class="container pt-5  mb-5">
        <h2>Quản lý đơn xin nghỉ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <form method="GET" action="{{ route('leave_requests.index') }}">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="0">Đang chờ</option> <!-- 0 -->
                                <option value="1">Đã phê duyệt</option> <!-- 1 -->
                                <option value="2">Đã từ chối</option> <!-- 2 -->
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <input style="margin-right: 25px;" type="date" name="start_date" class="form-control"
                                placeholder="Từ ngày">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" placeholder="Đến ngày">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                        </div>
                    </div>
                </form>

                <!-- Danh sách đơn -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Thời gian nghỉ</th>
                            <th>Lý do</th>
                            <th>Trạng thái</th>
                            <th>Người phê duyệt</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leaveRequests as $request)
                            <tr>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->start_date }} - {{ $request->end_date }}</td>
                                <td>{{ $request->reason }}</td>
                                <td>
                                    @if ($request->status === 0)
                                        <span class="badge bg-warning">Đang chờ</span>
                                    @elseif ($request->status === 1)
                                        <span class="badge bg-success">Đã phê duyệt</span>
                                    @else
                                        <span class="badge bg-danger">Đã từ chối</span>
                                    @endif
                                </td>
                                <td>{{ $request->approvedBy->name ?? '-' }}</td>
                                <td>
                                    @if ($request->status === 0)
                                        <form action="{{ route('leave_requests.updateStatus', $request->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit" class="btn btn-success btn-sm">Phê duyệt</button>
                                        </form>
                                        <form action="{{ route('leave_requests.updateStatus', $request->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="2">
                                            <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
                                        </form>
                                    @else
                                        -
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
