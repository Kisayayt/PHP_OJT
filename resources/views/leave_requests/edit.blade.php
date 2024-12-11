@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold"><i class="bi bi-house"></i> Chỉnh sửa đơn nghỉ phép</h2>
        <div class="row">
            <div class="col-md-3">
                @include('userHome.card')
            </div>

            <div class="col-md-9">
                <form action="{{ route('leave_requests.update', $leaveRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="leave_type" class="form-label">Loại nghỉ</label>
                        <select class="form-select" id="leave_type" name="leave_type" required>
                            <option value="morning" {{ $leaveRequest->leave_type == 'morning' ? 'selected' : '' }}>Nghỉ buổi
                                sáng</option>
                            <option value="afternoon" {{ $leaveRequest->leave_type == 'afternoon' ? 'selected' : '' }}>Nghỉ
                                buổi chiều</option>
                            <option value="full_day" {{ $leaveRequest->leave_type == 'full_day' ? 'selected' : '' }}>Nghỉ cả
                                ngày</option>
                            <option value="multiple_days"
                                {{ $leaveRequest->leave_type == 'multiple_days' ? 'selected' : '' }}>Nghỉ nhiều ngày
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_date" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $leaveRequest->start_date }}" required>
                    </div>

                    <div class="mb-3">
                        <label id="end_date_label" for="end_date" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $leaveRequest->end_date }}">
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Lý do nghỉ</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ $leaveRequest->reason }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('leave_requests.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

<script>
    document.getElementById('leave_type').addEventListener('change', function() {
        const leaveType = this.value;
        const endDateLabel = document.getElementById('end_date_label');
        const endDateInput = document.getElementById('end_date');

        // Kiểm tra loại nghỉ
        if (leaveType === 'multiple_days') {
            endDateLabel.style.display = 'block'; // Hiện label "Đến ngày"
            endDateInput.style.display = 'block'; // Hiện trường "end_date"
            endDateInput.required = true; // Đặt bắt buộc nhập cho trường này
        } else {
            endDateLabel.style.display = 'none'; // Ẩn label "Đến ngày"
            endDateInput.style.display = 'none'; // Ẩn trường "end_date"
            endDateInput.required = false; // Bỏ bắt buộc nhập cho trường này
        }
    });
    // Chạy kiểm tra ngay khi tải trang để đảm bảo trạng thái chính xác
    document.getElementById('leave_type').dispatchEvent(new Event('change'));
</script>
