@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <!-- Content: Work Time Management -->
            <div class="col-md-9">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('admin.updateWorkTime') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_start">Thời gian bắt đầu</label>
                                <input type="time" id="work_start" name="work_start" class="form-control"
                                    value="{{ $workStart }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_end">Thời gian kết thúc</label>
                                <input type="time" id="work_end" name="work_end" class="form-control"
                                    value="{{ $workEnd }}">
                            </div>
                        </div>


                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
                </form>
            </div>
        </div>
    </div>
@endsection
