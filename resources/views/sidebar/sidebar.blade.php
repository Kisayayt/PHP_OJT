<div class="list-group" id="sidebar">
    <a href="{{ route('users.dashboard') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('users.dashboard') || request()->routeIs('users.search') || request()->routeIs('userDetails') ? 'active' : '' }}">
        <i class="bi bi-people"></i> Quản lí người dùng
    </a>
    <a href="{{ route('departments.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('departments.index') || request()->routeIs('departments.search') ? 'active' : '' }}">
        <i class="bi bi-door-closed"></i> Quản lí phòng ban
    </a>
    <a href="{{ route('admin.checkinout') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('admin.checkinout') || request()->routeIs('admin.search') || request()->routeIs('admin.requests') || request()->routeIs('payroll.form') || request()->routeIs('payroll.calculate') || request()->routeIs('leave_requests.index') || request()->is('checkinout/search*') || request()->is('checkinout/filterByDate*') ? 'active' : '' }}">
        <i class="bi bi-person-check-fill"></i> Quản lí chấm công
    </a>
    <a href="{{ route('salaryLevels') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('salaryLevels') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Quản lí bậc lương
    </a>
    <a href="{{ route('payrolls.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('payrolls.index') ? 'active' : '' }}">
        <i class="bi bi-wallet"></i> Quản lí trả lương
    </a>
    <a href="{{ route('admin.workTime') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('admin.workTime') ? 'active' : '' }}">
        <i class="bi bi-alarm-fill"></i> Đổi thời gian làm việc
    </a>
    <a href="{{ route('reasons.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('reasons.index') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i> Quản lý các lý do
    </a>
    <a href="{{ route('chart.view') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('chart.view') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-fill"></i> Bảng tổng quan
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
