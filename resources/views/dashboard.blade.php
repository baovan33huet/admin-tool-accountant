@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Biểu đồ so sánh doanh thu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">So sánh doanh thu các cửa hàng tháng {{ \Carbon\Carbon::parse($currentMonth.'-01')->format('m/Y') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách cửa hàng -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Danh sách cửa hàng</h5>
                    <a href="{{ route('stores.create') }}" class="btn btn-sm btn-primary">+ Thêm cửa hàng</a>
                </div>
                <div class="card-body p-0">
                    @if($stores->isEmpty())
                        <div class="alert alert-info m-3 mb-0">
                            Chưa có cửa hàng nào. <a href="{{ route('stores.create') }}">Tạo cửa hàng đầu tiên</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tên cửa hàng</th>
                                        <th class="text-end">Số sản phẩm</th>
                                        <th class="text-end">Doanh thu tháng này</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stores as $index => $store)
                                        @php
                                            $storeRevenue = $storeRevenues->firstWhere('id', $store->id);
                                            $revenue = $storeRevenue ? $storeRevenue->revenue : 0;
                                            $productCount = $store->products()->count();
                                        @endphp
                                        <tr>
                                            <td>{{ $stores->firstItem() + $index }}</td>
                                            <td>
                                                <strong>{{ $store->name }}</strong>
                                            </td>
                                            <td class="text-end">{{ $productCount }}</td>
                                            <td class="text-end">
                                                <span class="text-success fw-bold">{{ number_format($revenue, 0, ',', '.') }} đ</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('products.index', $store) }}" class="btn btn-outline-primary">Sản phẩm</a>
                                                    <a href="{{ route('products.revenue', $store) }}" class="btn btn-outline-info">Doanh thu</a>
                                                    <a href="{{ route('product-imports.index', $store) }}" class="btn btn-outline-warning">Nhập hàng</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($stores->hasPages())
                            <div class="card-footer">
                                {{ $stores->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const storeNames = @json($storeRevenues->pluck('name'));
        const revenues = @json($storeRevenues->pluck('revenue'));

        // Mảng màu khác nhau cho từng cửa hàng
        const colors = [
            'rgba(54, 162, 235, 0.6)',   // Xanh dương
            'rgba(255, 99, 132, 0.6)',   // Đỏ hồng
            'rgba(75, 192, 192, 0.6)',   // Xanh lá
            'rgba(255, 206, 86, 0.6)',   // Vàng
            'rgba(153, 102, 255, 0.6)',  // Tím
            'rgba(255, 159, 64, 0.6)',   // Cam
            'rgba(199, 199, 199, 0.6)',  // Xám
            'rgba(83, 102, 255, 0.6)',   // Xanh đậm
            'rgba(255, 99, 255, 0.6)',   // Hồng
            'rgba(99, 255, 132, 0.6)'    // Xanh lá nhạt
        ];
        
        const borderColors = [
            'rgba(54, 162, 235, 1)',
            'rgba(255, 99, 132, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
            'rgba(199, 199, 199, 1)',
            'rgba(83, 102, 255, 1)',
            'rgba(255, 99, 255, 1)',
            'rgba(99, 255, 132, 1)'
        ];

        // Tạo mảng màu cho từng cột
        const backgroundColors = revenues.map((_, index) => colors[index % colors.length]);
        const borderColorsArray = revenues.map((_, index) => borderColors[index % borderColors.length]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: storeNames,
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: revenues,
                    backgroundColor: backgroundColors,
                    borderColor: borderColorsArray,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        categoryPercentage: 0.5,  // Thu nhỏ độ rộng cột (50% của không gian có sẵn)
                        barPercentage: 0.7        // Thu nhỏ độ rộng thanh (70% của category)
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
