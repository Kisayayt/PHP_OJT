<!DOCTYPE html>
<html>

<head>
    <title>{{ $type === 'check_in' ? 'Nhắc nhở giờ check-in' : 'Nhắc nhở giờ check-out' }}</title>
</head>

<body>
    <h1>{{ $type === 'check_in' ? 'Nhắc nhở giờ check-in' : 'Nhắc nhở giờ check-out' }}</h1>
    <p>Chào bạn, đây là thông báo nhắc nhở về giờ {{ $type === 'check_in' ? 'check-in' : 'check-out' }} của bạn. Hãy nhớ
        vào chấm công đúng giờ.</p>
</body>

</html>
