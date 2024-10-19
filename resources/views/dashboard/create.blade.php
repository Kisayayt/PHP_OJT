@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/insert" method="post">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Nhập họ và tên" required>
            </div>
        </form>
    </div>
@endsection
