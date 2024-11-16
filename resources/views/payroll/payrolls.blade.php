@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Danh Sách Tính Lương</h2>

        <!-- Flash messages -->
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

        <!-- Content -->
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <div class="col-md-9">
                <!-- Form tìm kiếm -->
                <form action="{{ route('payrolls.index') }}" method="GET" class="form-inline mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..."
                            value="{{ $search }}" style="max-width: 250px;">
                        <button type="submit" class="btn btn-primary ml-2">Tìm kiếm</button>
                    </div>
                </form>

                <!-- Bảng payrolls -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Hệ số lương</th>
                            <th>Số ngày công hợp lệ</th>
                            <th>Số ngày công không hợp lệ</th>
                            <th>Lương nhận được</th>
                            <th>Ngày tính lương</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->user->name }}</td>
                                <td>{{ $payroll->salary_coefficient }}</td>
                                <td>{{ $payroll->valid_days }}</td>
                                <td>{{ $payroll->invalid_days }}</td>
                                <td>{{ number_format($payroll->salary_received, 0) }} VND</td>
                                <td>{{ $payroll->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Phân trang -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $payrolls->appends(request()->input())->onEachSide(2)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
