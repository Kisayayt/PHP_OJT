@extends('layouts.app')

@section('content')
    <div class="container mt-5 mb-5">
        <h1 class="mb-4"><strong>{{ $department->name }}</strong></h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-people"></i> Người trong phòng ban</h5>
                    </div>
                    <div class="card-body">
                        @if ($department->users->isEmpty())
                            <p class="text-muted"><i class="bi bi-emoji-frown"></i> Chưa có người nào trong phòng ban này.
                            </p>
                        @else
                            <ul class="list-group">
                                @foreach ($department->users as $user)
                                    <li class="list-group-item">
                                        <strong>{{ $user->name }}</strong> <span
                                            class="text-muted">({{ $user->email }})</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 style="font-weight: bold" class="mb-0"><i class="bi bi-door-open"></i> Danh sách phòng ban con
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($department->children->isEmpty())
                            <p class="text-muted"><i class="bi bi-emoji-frown"></i> Không có phòng ban con nào.</p>
                        @else
                            <ul class="list-group">
                                @foreach ($department->children as $child)
                                    <li class="list-group-item">
                                        <strong>{{ $child->name }}</strong>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>




        <div class="mt-4">
            <a href="{{ route('departments.index') }}" class="btn btn-primary">Quay lại danh sách phòng ban</a>
        </div>
    </div>
@endsection
