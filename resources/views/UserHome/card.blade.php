@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
    </div>
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
<div class="card text-center">
    <img src="{{ asset(auth()->user()->avatar) }}" class="card-img-top" alt="User Image" width="300" height="300"
        style="object-fit: cover; cursor: pointer;" id="profileImage">
    <div class="card-body">
        <h5 style="font-weight: bold" class="card-title">{{ auth()->user()->name }}</h5>
        <p class="card-text">{{ auth()->user()->department ? auth()->user()->department->name : 'Không trong ban nào' }}
        </p>
        <div>
            <a href="{{ route('details') }}" class="btn btn-primary btn-block mt-3 w-100">Thông tin người dùng</a>
            <a href="#" class="btn btn-primary btn-block mt-2 w-100" data-bs-toggle="modal"
                data-bs-target="#leaveRequestModal">
                Gửi đơn xin nghỉ
            </a>
            <a href="{{ route('leave_requests.index') }}" class="btn btn-secondary btn-block mt-2 w-100">Quản lí các đơn
                xin
                nghỉ</a>

            <a href="{{ route('checkinout') }}" class="btn btn-secondary btn-block mt-2 w-100">Lịch sử
                check-in/check-out</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger btn-block mt-2 w-100">Đăng xuất</button>
            </form>
        </div>
    </div>

    <!-- Ẩn input file -->
    <form action="{{ route('update.avatar') }}" method="POST" enctype="multipart/form-data" id="avatarForm"
        class="mt-3" style="display: none;">
        @csrf
        <input type="file" name="avatar" accept="image/*" id="avatarInput" required>
        <button type="submit" class="btn btn-success mt-2" id="uploadButton">Cập nhật ảnh</button>
    </form>

</div>

<!-- Modal -->
<div class="modal fade" id="leaveRequestModal" tabindex="-1" aria-labelledby="leaveRequestModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leave.request') }}" method="POST" id="leaveForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveRequestModalLabel">Gửi đơn xin nghỉ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Options -->
                    <div class="mb-3">
                        <label for="leave_type" class="form-label">Loại nghỉ</label>
                        <select class="form-select" id="leave_type" name="leave_type" required>
                            <option value="morning">Nghỉ buổi sáng</option>
                            <option value="afternoon">Nghỉ buổi chiều</option>
                            <option value="full_day">Nghỉ cả ngày</option>
                            <option value="multiple_days">Nghỉ nhiều ngày</option>
                        </select>
                    </div>

                    <!-- Chọn ngày -->
                    <div class="mb-3" id="datePickerContainer">
                        <label for="start_date" class="form-label">Từ ngày</label>
                        <input type="date" class="form-control" id="start_date" name="start_date">

                        <label for="end_date" class="form-label mt-2" id="end_date_label" style="display: none;">
                            Đến ngày
                        </label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            style="display: none;">
                    </div>

                    <!-- Lý do nghỉ -->
                    <div class="mb-3">
                        <label for="reason" class="form-label">Lý do nghỉ</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    @if (auth()->user()->leave_balance == 0)
                        <p style="font-weight: bold ; color: red ; font-style: italic">* Lưu ý: Bạn đã hết ngày nghỉ.
                        </p>
                    @else
                        <p style="font-weight: bold ; color: red ; font-style: italic">* Lưu ý: Bạn còn
                            {{ auth()->user()->leave_balance }} ngày nghỉ.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi đơn</button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    document.getElementById('profileImage').onclick = function() {
        document.getElementById('avatarInput').click(); // Kích hoạt input file khi nhấp vào ảnh
    };

    document.getElementById('avatarInput').onchange = function() {
        document.getElementById('avatarForm').submit(); // Gửi form khi chọn file
    };

    document.getElementById('leave_type').addEventListener('change', function() {
        const leaveType = this.value;
        const endDateLabel = document.getElementById('end_date_label');
        const endDateInput = document.getElementById('end_date');

        if (leaveType === 'multiple_days') {
            endDateLabel.style.display = 'block';
            endDateInput.style.display = 'block';
            endDateInput.required = true;
        } else {
            endDateLabel.style.display = 'none';
            endDateInput.style.display = 'none';
            endDateInput.required = false;
        }
    });
</script>
