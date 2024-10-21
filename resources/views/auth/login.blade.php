<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'PHP OJT') }}</title>

    <!-- Link to Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Link to Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    {{-- <link rel="stylesheet" href="css/app.css"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
        }

        h1 {
            font-size: 2.5rem;
        }

        p {
            font-size: 1.0rem;
        }
    </style>
</head>

<body>
    @if ($errors->any())
        <div class="header">
            <h1 class="header-title"><i class="bi bi-kanban"></i> LOGIN</h1>
            <form action="{{ route('login') }}" method="POST" class="login-form">
                @csrf <!-- Thêm token CSRF -->
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
</body>

</html>




<style>
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
    }

    .header {
        background-image: url('images/anhheader.jpg');
        background-size: cover;
        background-position: center;
        height: 100vh;
        display: flex;
        flex-direction: column;
        /* Để sắp xếp các phần tử theo cột */
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
        margin-bottom: 20px;
        /* Thêm khoảng cách dưới tiêu đề */
    }

    .login-form {
        background: rgba(255, 255, 255, 0.2);
        /* Nền trắng nhẹ với độ trong suốt */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        /* Làm mờ nền */
        border: 2px solid white;
        /* Viền trắng */
        z-index: 2;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
    }

    button:hover {
        background-color: #0056b3;
        /* Màu khi hover */
    }
</style>
