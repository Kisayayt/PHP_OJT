<div class="header">
    <h1 class="header-title"><i class="bi bi-kanban"></i> PERSONNEL MANAGEMENT</h1>
</div>

<style>
    .header {
        background-image: url('images/anhheader.jpg');
        /* Đảm bảo đường dẫn là đúng */
        background-size: cover;
        background-position: center;
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        position: relative;
        /* Thêm thuộc tính này */
    }

    .header::after {
        /* Tạo lớp phủ mờ */
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        /* Tạo màu phủ mờ */
        backdrop-filter: blur(8px);
        /* Hiệu ứng làm mờ */
        z-index: 1;
        /* Đặt lớp mờ phía dưới văn bản */
    }

    .header-title {
        font-size: 3rem;
        text-align: center;
        font-weight: bold;
        position: relative;
        /* Để văn bản nằm trên lớp phủ */
        z-index: 2;
        /* Đặt văn bản trên lớp phủ */
    }
</style>
