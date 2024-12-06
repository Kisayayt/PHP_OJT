@extends('userLayouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <div>
            <h2 style="font-weight: bold"><i class="bi bi-house"></i> Trang chủ</h2>
            @if ($lastCheckoutTime)
                @php
                    $hour = floor($lastCheckoutTime / 60);
                    $minute = $lastCheckoutTime % 60;
                @endphp
                <p>Thời gian gần đây nhất: <strong>{{ $hour }} giờ {{ $minute }} phút</strong></p>
            @else
                <p>Thời gian gần đây nhất: <strong>0 giờ</strong></p>
            @endif
        </div>
        <div class="row">
            <div class="col-md-3">
                @include('Userhome.card')
            </div>

            <div class="col-md-9">
                @if ($isCheckedIn)
                    <div class="alert alert-success">
                        <p>Bạn đã check-in!</p>
                        <form action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger">Check Out</button>
                        </form>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <p>Bạn chưa check-in!</p>
                        <form action="{{ route('checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">Check In</button>
                        </form>
                    </div>
                @endif

                <!-- Table lịch sử check-in/check-out -->
                <div id="history-table" class="mt-4">
                    <h4>Lịch sử check-in/check-out</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Thời gian</th>
                                <th>Ngày/tháng/năm</th>
                                <th>Loại</th>
                                <th>Thời lượng</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $record)
                                <tr>
                                    <td>{{ $record->created_at->format('H:i') }}</td>
                                    <td>{{ $record->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $record->type == 'in' ? 'Check-in' : 'Check-out' }}</td>
                                    <td>
                                        @if ($record->type == 'out')
                                            {{-- Chuyển đổi phút thành giờ và phút để hiển thị --}}
                                            @php
                                                $hours = floor($record->time / 60); // Tính giờ
                                                $minutes = $record->time % 60; // Tính phút
                                            @endphp
                                            {{ $hours }} giờ {{ $minutes }} phút
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->status == 1)
                                            <p class="text-success">Hợp lệ</p>
                                        @elseif ($record->status == 0)
                                            <p class="text-danger">Không hợp lệ</p>
                                        @elseif ($record->status == 3)
                                            <p>Đang xem xét</p>
                                        @elseif ($record->status == 4)
                                            <p class="text-danger">Từ chối</p>
                                        @elseif ($record->status == 5)
                                            <p class="text-success">Hợp lệ</p>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($record->status == 0 && $record->type == 'out')
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#modalReason{{ $record->id }}">
                                                Giải trình
                                            </button>

                                            <!-- Modal -->
                                            <!-- Modal -->
                                            <div class="modal fade" id="modalReason{{ $record->id }}" tabindex="-1"
                                                aria-labelledby="modalReasonLabel{{ $record->id }}" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="modalReasonLabel{{ $record->id }}">
                                                                Nhập lý do giải trình
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('submit-reason', $record->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <!-- Danh sách lựa chọn lý do -->
                                                                <div class="form-group">
                                                                    <label><strong>Chọn lý do:</strong></label>
                                                                    @foreach ($reasons as $reason)
                                                                        <div class="form-check mt-2">
                                                                            <input class="form-check-input" type="radio"
                                                                                name="reason"
                                                                                id="reason{{ $reason->id }}"
                                                                                value="{{ $reason->reason }}" required>
                                                                            <label class="form-check-label"
                                                                                for="reason{{ $reason->id }}">{{ $reason->reason }}</label>
                                                                        </div>
                                                                    @endforeach
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio"
                                                                            name="reason" id="otherReason" value="other">
                                                                        <label class="form-check-label" for="otherReason">Lý
                                                                            do khác</label>
                                                                    </div>
                                                                </div>

                                                                <!-- Textarea lý do khác (ẩn mặc định) -->
                                                                <div class="form-group mt-3" id="otherReasonText"
                                                                    style="display: none;">
                                                                    <label for="customReason">Nhập lý do:</label>
                                                                    <textarea name="custom_reason" id="customReason" class="form-control"></textarea>
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-primary">Gửi</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $history->onEachSide(2)->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const otherReasonRadio = document.querySelectorAll("input[name='reason']");
        const otherReasonText = document.getElementById("otherReasonText");

        otherReasonRadio.forEach(radio => {
            radio.addEventListener("change", function() {
                if (this.value === "other") {
                    otherReasonText.style.display = "block";
                } else {
                    otherReasonText.style.display = "none";
                    document.getElementById("customReason").value =
                        "";
                }
            });
        });
    });
</script>
