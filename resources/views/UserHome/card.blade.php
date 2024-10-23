<div class="card text-center">
    <img src="{{ asset(auth()->user()->avatar) }}" class="card-img-top" alt="User Image" width="300" height="300"
        style="object-fit: cover; cursor: pointer;" id="profileImage">
    <div class="card-body">
        <h5 style="font-weight: bold" class="card-title">{{ auth()->user()->name }}</h5>
        <p class="card-text">{{ auth()->user()->department->name }}</p>
        <div>
            <a href="{{ route('details') }}" class="btn btn-primary btn-block mt-3 w-100">Thông tin người dùng</a>
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

    <!-- Hiển thị thông báo thành công -->
    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @endif
</div>


<script>
    document.getElementById('profileImage').onclick = function() {
        document.getElementById('avatarInput').click(); // Kích hoạt input file khi nhấp vào ảnh
    };

    document.getElementById('avatarInput').onchange = function() {
        document.getElementById('avatarForm').submit(); // Gửi form khi chọn file
    };
</script>
