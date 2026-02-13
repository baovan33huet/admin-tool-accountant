@extends('layouts.app')

@section('title', 'Thêm cửa hàng')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="card-title mb-3">Thêm cửa hàng mới</h4>

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('stores.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên cửa hàng</label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>
                </div>

                <button type="submit" class="btn btn-primary">Lưu</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Hủy</a>
            </form>
        </div>
    </div>
@endsection
