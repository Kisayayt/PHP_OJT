@extends('layouts.app')

@section('content')
    <div class="container pt-5 mb-5">
        <h2 style="font-weight: bold">Quản lý đơn chờ xử lý</h2>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row">
            <div class="col-md-3">
                @include('sidebar.sidebar')
            </div>
            <div class="col-md-9">
                <div class="accordion" id="pendingRequestsAccordion">
                    @forelse ($pendingRequests as $request)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $request->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $request->id }}" aria-expanded="false"
                                    aria-controls="collapse{{ $request->id }}">
                                    {{ $request->user->name }} ({{ $request->updated_at->format('H:i d/m/Y') }} -
                                    {{ $request->user->username }})
                                </button>
                            </h2>
                            <div id="collapse{{ $request->id }}" class="accordion-collapse collapse"
                                aria-labelledby="heading{{ $request->id }}" data-bs-parent="#pendingRequestsAccordion">
                                <div class="accordion-body">
                                    @if ($request->checkInRecord)
                                        <p><strong>Thời gian Check-in:</strong>
                                            {{ $request->checkInRecord->created_at->format('H:i d/m/Y') }}</p>
                                    @else
                                        <p><strong>Thời gian Check-in:</strong> Không xác định</p>
                                    @endif
                                    <p><strong>Thời gian Check-out:</strong>
                                        {{ $request->created_at->format('H:i d/m/Y') }}</p>
                                    <p><strong>Lý do:</strong> {{ $request->explanation }}</p>

                                    <div class="d-flex">
                                        <form action="{{ route('admin.requests.accept', $request->id) }}" method="POST"
                                            class="mr-2" style="margin-right: 10px;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm">Chấp nhận</button>
                                        </form>
                                        <form action="{{ route('admin.requests.reject', $request->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Từ chối</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p>Không có đơn nào đang chờ xử lý.</p>
                    @endforelse
                </div>
            </div>
        </div>




        <!-- Điều khiển phân trang -->
        <div class="d-flex justify-content-center mt-4">
            {{ $pendingRequests->onEachSide(2)->links() }}
        </div>
    </div>
@endsection
