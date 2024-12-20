@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <div class="row">
                    @include('charts.employee_ratio_chart')
                    @include('charts.gender_chart')

                </div>

                <div class="row">
                    @include('charts.contract_type_chart')
                    @include('charts.attendance_chart')
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    #employeeRatioChart,
    #contractTypeChart,
    #attendanceChart,
    #genderChart {
        max-width: 100%;
        height: 100px;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 20px;
        margin-top: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
