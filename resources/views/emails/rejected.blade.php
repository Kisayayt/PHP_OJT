<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn đã nộp thành công</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        .header {
            background-image: url('https://nationalsecuritynews.com/wp-content/uploads/2024/07/Background-Verification-Process-in-MNCs.jpeg');
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

        .content {
            padding: 20px;
            background-color: #f4f4f9;
            margin: 20px auto;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content h2 {
            color: #007bff;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #555;
        }

        .footer-signature {
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="content">
        <div class="header">
            <h1 class="header-title"><i class="bi bi-kanban"></i></h1>
        </div>
        <h2 style="color: red">Đơn của bạn không được admin chấp nhận!</h2>
        <p>Xin chào {{ $name }},</p>
        <p>Đơn giải trình của bạn đã không được admin chấp thuận</p>
        <div class="footer">
            <p class="footer-signature">Trân trọng,</p>
            <p class="footer-signature">Công ty TNHH Một Mình Tôi</p>
            <img style="width: 100px ; height: 100px; blur: 2px;"
                src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Jon_Kirsch%27s_Signature.png" alt="image">
        </div>
    </div>

</body>

</html>
