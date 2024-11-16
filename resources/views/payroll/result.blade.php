@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Tính lương</h2>
        <div class="row">

            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>

            <div class="col-md-9">
                <h2 style="font-weight: bold">Kết Quả Tính Lương</h2>
                <table class="table">
                    <tr>
                        <th>Nhân viên</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Hệ số lương</th>
                        <td>{{ $salaryCoefficient }}</td>
                    </tr>
                    <tr>
                        <th>Số ngày công hợp lệ</th>
                        <td>{{ $validDays }}</td>
                    </tr>
                    <tr>
                        <th>Số ngày công không hợp lệ</th>
                        <td>{{ $invalidDays }}</td>
                    </tr>
                    <tr>
                        <th>Lương nhận được</th>
                        <td>{{ number_format($salaryReceived, 0) }} VND</td>
                    </tr>
                </table>
                <a href="{{ route('payroll.store') }}" class="btn btn-success"
                    onclick="event.preventDefault(); 
                         document.getElementById('payroll-form').submit();">
                    Lưu trữ tính lương
                </a>

                <form id="payroll-form" action="{{ route('payroll.store') }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="salary_received" value="{{ $salaryReceived }}">
                    <input type="hidden" name="valid_days" value="{{ $validDays }}">
                    <input type="hidden" name="invalid_days" value="{{ $invalidDays }}">
                    <input type="hidden" name="salary_coefficient" value="{{ $salaryCoefficient }}">
                </form>

                <a onclick="window.location.href='/payroll/calculate'" class="btn btn-primary">Chọn lại</a>
            </div>
        </div>


    </div>
@endsection
