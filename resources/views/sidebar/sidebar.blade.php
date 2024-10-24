<div class="list-group" id="sidebar">
    <a href="{{ route('users.dashboard') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('users.dashboard') || request()->routeIs('users.search') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Quản lí người dùng
    </a>
    <a href="{{ route('departments.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('departments.index') || request()->routeIs('departments.search') ? 'active' : '' }}">
        <i class="bi bi-door-closed"></i> Quản lí phòng ban
    </a>
    <a href="{{ route('admin.checkinout') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('admin.checkinout') || request()->routeIs('admin.search') || request()->is('checkinout/search*') || request()->is('checkinout/filterByDate*') ? 'active' : '' }}">
        <i class="bi bi-person-check-fill"></i> Quản lí checkout
    </a>
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit" class="list-group-item list-group-item-action"
            style="background: none; cursor: pointer;">
            <i class="bi bi-box-arrow-left"></i> Đăng xuất
        </button>
    </form>
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
