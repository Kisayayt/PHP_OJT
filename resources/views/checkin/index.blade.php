@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form action="{{ route('admin.checkinoutSearch') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Tìm kiếm nhân viên hoặc thời gian..." value="{{ request('search') }}"
                                style="max-width: 200px;">
                            <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center ml-auto">
                        <form action="{{ route('admin.filterByDate') }}" method="GET" class="d-flex align-items-center">
                            <button style="margin-right: 10px;" type="submit" class="btn btn-primary">Lọc</button>
                            <div class="form-group mr-2">
                                <input type="date" id="date" name="date" class="form-control"
                                    value="{{ request('date') }}">
                            </div>

                        </form>

                        <!-- Thêm style với margin-left để tạo khoảng cách cụ thể -->
                        <form action="{{ route('exportCheck') }}" method="GET"
                            style="margin-left: 20px; margin-right: 10px;">
                            <button type="submit" class="btn btn-success">Xuất file</button>
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Quản lý các đơn
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.requests') }}">Quản lý đơn giải trình</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('leave_requests.index') }}">Quản lý đơn xin
                                        nghỉ</a>
                                </li>
                            </ul>
                        </div>


                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('payroll.form') }}" class="btn btn-success me-2">Tính lương</a>
                    <form action="{{ route('send.reminders') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Nhắc nhở chấm công</button>
                    </form>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tên tài khoản</th>
                            <th>Nhân viên</th>
                            <th>Thời gian</th>
                            <th>
                                <a href="{{ route('admin.checkinout', [
                                    'sort_by' => 'created_at',
                                    'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc',
                                    'search' => request('search'),
                                    'date' => request('date'),
                                ]) }}"
                                    style="color: black; text-decoration: none">
                                    Ngày/Tháng/Năm

                                    @if (request('sort_by') == 'created_at')
                                        <i
                                            class="bi bi-chevron-{{ request('sort_direction') == 'asc' ? 'down' : 'up' }}"></i>
                                    @else
                                        <i class="bi bi-chevron-up"></i>
                                    @endif
                                </a>
                            </th>

                            <th>Giờ làm việc</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->user->username }}</td>
                                <td>{{ $record->user->name }}</td>
                                <td>{{ $record->created_at->format('H:i') }}</td>
                                <td>{{ $record->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($record->type === 'in')
                                        --
                                    @else
                                        {{ intdiv($record->time, 60) }} giờ {{ $record->time % 60 }} phút
                                    @endif
                                </td>
                                <td>{{ $record->type === 'in' ? 'Đang check-in' : 'Đã check-out' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Không tìm thấy kết quả phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>

                <div class="d-flex justify-content-center">
                    {{ $attendanceRecords->onEachSide(2)->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
