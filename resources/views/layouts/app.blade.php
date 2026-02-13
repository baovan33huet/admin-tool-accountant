<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Quản Lý Cửa Hàng')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Quản Lý Cửa Hàng</a>
        <div class="d-flex">
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button class="btn btn-outline-light btn-sm" type="submit">Đăng xuất</button>
            </form>
        </div>
    </div>
</nav>

<div class="container-fluid mt-3">
    <div class="row">
        <div class="col-md-3 col-lg-2 mb-3">
            <div class="card shadow-sm mb-3">
                <div class="list-group list-group-flush">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <strong>📊 Dashboard</strong>
                    </a>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Cửa hàng</strong>
                    <a href="{{ route('stores.create') }}" class="btn btn-sm btn-primary">+ Thêm</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($stores ?? [] as $store)
                        @php
                            $isActive = false;
                            if (request()->routeIs('products.*') || request()->routeIs('product-imports.*') || request()->routeIs('product-sales.*')) {
                                $routeStore = request()->route('store');
                                $isActive = $routeStore && $routeStore->id == $store->id;
                            }
                        @endphp
                        <a href="{{ route('products.index', $store) }}" class="list-group-item list-group-item-action {{ $isActive ? 'active' : '' }}">
                            {{ $store->name }}
                        </a>
                    @empty
                        <div class="list-group-item text-muted">
                            Chưa có cửa hàng nào
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-9 col-lg-10">
            @yield('content')
        </div>
    </div>
</div>
</body>
</html>
