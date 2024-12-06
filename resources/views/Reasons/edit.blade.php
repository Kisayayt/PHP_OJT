@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <Button onclick="window.location.href='/reasons'" class="btn btn-primary mb-3"><i class="bi bi-house"></i>
            Trở về
            trang chủ</Button>
        <h2 style="font-weight: bold">Chỉnh sửa lý do</h2>

        <form action="{{ route('reasons.update', $reason->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="reason" class="form-label">Tên lý do</label>
                <input type="text" class="form-control" id="reason" name="reason" value="{{ $reason->reason }}"
                    required>
            </div>

            <button type="submit" class="btn btn-success">Cập nhật</button>
        </form>
    </div>
@endsection
