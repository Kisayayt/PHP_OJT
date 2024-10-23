<div class="header">
    <h1 class="header-title"><i class="bi bi-kanban"></i> Xin chào, {{ auth()->user()->name }}!</h1>
</div>

<style>
    .header {
        background-image: url('{{ asset('images/office-employees.jpg') }}');

        background-size: cover;
        background-position: center;
        height: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        position: relative;

    }

    .header::after {

        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(8px);
        z-index: 1;
    }

    .header-title {
        font-size: 3rem;
        text-align: center;
        font-weight: bold;
        position: relative;
        z-index: 2;
    }
</style>
