<div class="list-group" id="sidebar">
    <a href="{{ route('users.dashboard') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('users.dashboard') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Quản lí người dùng
    </a>
    <a href="{{ route('departments.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('departments.index') ? 'active' : '' }}">
        <i class="bi bi-door-closed"></i> Quản lí phòng ban
    </a>
    <a href="#" class="list-group-item list-group-item-action">
        <i class="bi bi-person-check-fill"></i> Quản lí checkout
    </a>
</div>



{{-- <script>
    document.querySelectorAll('#sidebar .list-group-item').forEach(item => {
        item.addEventListener('click', function() {
            // Xóa lớp 'active' khỏi tất cả các mục
            document.querySelectorAll('#sidebar .list-group-item').forEach(link => {
                link.classList.remove('active');
            });

            // Thêm lớp 'active' cho mục đã nhấn
            item.classList.add('active');
        });
    });
</script> --}}
