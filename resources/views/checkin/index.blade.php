@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang quản lí Check-in/Check-out</h2>
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
                    <form action="{{ route('admin.filterByDate') }}" method="GET" class="d-flex align-items-center ml-3">
                        <div class="form-group">
                            {{-- <label for="date">Chọn ngày:</label> --}}
                            <input type="date" id="date" name="date" class="form-control"
                                value="{{ request('date') }}">
                        </div>
                        <button type="submit" class="btn btn-primary ml-2">Lọc</button>
                    </form>
                </div>



                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Thời gian</th>
                            <th>Ngày/Tháng/Năm</th>
                            <th>Tổng thời gian (giờ)</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendanceRecords as $record)
                            <tr>
                                <td>{{ $record->user->name }}</td>
                                <td>{{ $record->created_at->format('H:i') }}</td>
                                <td>{{ $record->created_at->format('d/m/Y') }}</td>
                                <td>{{ $record->type === 'in' ? '--' : $record->time }} giờ</td>
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
                    {{ $attendanceRecords->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
