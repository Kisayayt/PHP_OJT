@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Trang chủ</h2>
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="chartTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="employee-ratio-tab" data-bs-toggle="tab" href="#employee-ratio"
                            role="tab" aria-controls="employee-ratio" aria-selected="true">Tỷ lệ nhân viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="gender-chart-tab" data-bs-toggle="tab" href="#gender-chart" role="tab"
                            aria-controls="gender-chart" aria-selected="false">Thống kê giới tính</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="contract-type-tab" data-bs-toggle="tab" href="#contract-type" role="tab"
                            aria-controls="contract-type" aria-selected="false">Loại hợp đồng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab"
                            aria-controls="attendance" aria-selected="false">Thống kê điểm danh</a>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content mt-4">
                    <div class="tab-pane fade show active" id="employee-ratio" role="tabpanel"
                        aria-labelledby="employee-ratio-tab">
                        @include('charts.employee_ratio_chart')
                    </div>
                    <div class="tab-pane fade" id="gender-chart" role="tabpanel" aria-labelledby="gender-chart-tab">
                        @include('charts.gender_chart')
                    </div>
                    <div class="tab-pane fade" id="contract-type" role="tabpanel" aria-labelledby="contract-type-tab">
                        @include('charts.contract_type_chart')
                    </div>
                    <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                        @include('charts.attendance_chart')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<style>
    #employeeRatioChart,
    #contractTypeChart,
    #genderChart {
        width: 100%;
        /* Chiều rộng mặc định */
        max-width: 600px;
        /* Đặt giới hạn chiều rộng tối đa */
        height: 400px;
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Chiều cao cụ thể */
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
