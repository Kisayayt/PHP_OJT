@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Tính lương</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <p style="color: red;">{{ $error }}</p>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('payroll.calculate') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="user_id">Chọn nhân viên:</label>
                        <select name="user_id" id="user_id" class="form-control">
                            <option value="">-- Chọn nhân viên --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Tính Lương</button>
                    <a href="{{ route('run.payroll.calculate') }}" class="btn btn-primary mt-3">Tính lương cho tất cả nhân
                        viên</a>
                </form>
            </div>
        </div>
    </div>
@endsection
