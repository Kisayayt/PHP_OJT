@extends('userlayouts.app')

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
                        <select class="form-select" id="leave_type_wat" name="leave_type" required>
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

                    <div class="mb-3" id="end_date_div" style="display: none;">
                        <label id="end_date_label" for="end_date" class="form-label">Đến ngày</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $leaveRequest->end_date }}">
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Lý do nghỉ</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required>{{ $leaveRequest->reason }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('leave_requests_user.index') }}" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                    {{-- //example
                    <select id="myDropdown">
                        <option value="option1">Tùy chọn 1</option>
                        <option value="option2">Tùy chọn 2</option>
                    </select> --}}

                </form>
            </div>
        </div>
    </div>

    <script>
        // //examplee
        // document.getElementById("myDropdown").addEventListener("change", function() {
        //     // Code xử lý khi thay đổi giá trị của dropdown
        //     console.log(this.value);
        // });
        document.getElementById("leave_type_wat").addEventListener("change", function() {
            // Khi thay đổi giá trị dropdown, in giá trị ra console
            console.log(this.value); // In ra giá trị đã chọn
            toggleEndDate(); // Cập nhật hiển thị trường "Đến ngày" nếu cần
        });

        const leaveTypeSelect = document.getElementById('leave_type_wat');
        const endDateDiv = document.getElementById('end_date_div');
        const endDateInput = document.getElementById('end_date');

        // Hàm điều khiển việc hiển thị trường "Đến ngày"
        function toggleEndDate() {
            console.log('Leave type selected:', leaveTypeSelect.value); // In ra giá trị khi thay đổi
            if (leaveTypeSelect.value === 'multiple_days') {
                endDateDiv.style.display = 'block'; // Hiển thị trường "Đến ngày"
            } else {
                endDateDiv.style.display = 'none'; // Ẩn trường "Đến ngày"
                endDateInput.value = ''; // Xóa giá trị trường "Đến ngày"
            }
        }

        // Gọi hàm toggle ngay khi tải trang để điều chỉnh trạng thái ban đầu của trường "Đến ngày"
        toggleEndDate();
    </script>
@endsection
