@extends('layouts.admin')

@section('title', __('messages.dashboard'))

@push('styles')
<style>
.admin-stat-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); position: relative; overflow: hidden; transition: all .3s; }
.admin-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.1); }
.admin-stat-card .stat-icon { position: absolute; right: -10px; top: -10px; font-size: 4rem; opacity: 0.08; }
.admin-stat-card .stat-label { font-size: 0.85rem; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.admin-stat-card .stat-num { font-size: 2rem; font-weight: 800; margin-top: 8px; }
.admin-stat-card.gradient-blue { background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; }
.admin-stat-card.gradient-green { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; }
.admin-stat-card.gradient-orange { background: linear-gradient(135deg, #f12711, #f5af19); color: #fff; }
.admin-stat-card.gradient-pink { background: linear-gradient(135deg, #f093fb, #f5576c); color: #fff; }
.admin-card { background: #fff; border-radius: 16px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.table-custom thead th { border: none; background: #f8f9fa; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
.table-custom tbody td { vertical-align: middle; }
.order-status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.low-stock-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
.low-stock-item:last-child { border-bottom: none; }
.quick-link { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 12px; background: #f8f9fa; text-decoration: none; color: #333; transition: all .2s; }
.quick-link:hover { background: #667eea; color: #fff; transform: translateX(5px); }
.quick-link i { font-size: 1.3rem; }
</style>
@endpush

@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2"></i> {{ __('messages.dashboard') }}</h4>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-blue">
            <i class="bi bi-bag-check stat-icon"></i>
            <div class="stat-label">{{ __('messages.total_orders') }}</div>
            <div class="stat-num">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-green">
            <i class="bi bi-cash-stack stat-icon"></i>
            <div class="stat-label">{{ __('messages.total_revenue') }}</div>
            <div class="stat-num">${{ number_format($totalRevenue, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-orange">
            <i class="bi bi-box-seam stat-icon"></i>
            <div class="stat-label">{{ __('messages.total_products') }}</div>
            <div class="stat-num">{{ $totalProducts }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-pink">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-label">{{ __('messages.total_users') }}</div>
            <div class="stat-num">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#fa709a,#fee140);color:#fff;">
            <i class="bi bi-star stat-icon"></i>
            <div class="stat-label">{{ __('messages.reviews') }}</div>
            <div class="stat-num">{{ $totalReviews ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#30cfd0,#330867);color:#fff;">
            <i class="bi bi-star-fill stat-icon"></i>
            <div class="stat-label">{{ __('messages.avg_rating') }}</div>
            <div class="stat-num">{{ $avgRating ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#ee5a24,#f9ca24);color:#fff;">
            <i class="bi bi-exclamation-triangle stat-icon"></i>
            <div class="stat-label">{{ __('messages.low_stock') }}</div>
            <div class="stat-num">{{ $lowStockCount ?? 0 }}</div>
        </div>
    </div>
</div>
<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="admin-card">
            <h6 class="fw-bold mb-3">{{ __('messages.sales_trend') }}</h6>
            <canvas id="salesChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <h6 class="fw-bold mb-3">{{ __('messages.order_status_distribution') }}</h6>
            <canvas id="statusChart" height="200"></canvas>
        </div>
    </div>
</div>

<!-- Category Distribution -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="admin-card">
            <h6 class="fw-bold mb-3">{{ __('messages.category_distribution') }}</h6>
            <canvas id="catChart" height="80"></canvas>
        </div>
    </div>
</div>

<!-- Quick Links -->
<div class="row g-3 mb-4">
    <div class="col-md-12">
        <h6 class="fw-bold mb-3">{{ __('messages.quick_links') }}</h6>
    </div>
    <div class="col-md-2 col-6"><a href="{{ route('admin.products.index') }}" class="quick-link"><i class="bi bi-box-seam"></i> {{ __('messages.products') }}</a></div>
    <div class="col-md-2 col-6"><a href="{{ route('admin.orders.index') }}" class="quick-link"><i class="bi bi-receipt"></i> {{ __('messages.orders') }}</a></div>
    <div class="col-md-2 col-6"><a href="{{ route('admin.categories.index') }}" class="quick-link"><i class="bi bi-tags"></i> {{ __('messages.categories') }}</a></div>
    <div class="col-md-2 col-6"><a href="{{ route('admin.coupons.index') }}" class="quick-link"><i class="bi bi-ticket"></i> {{ __('messages.coupons') }}</a></div>
    <div class="col-md-2 col-6"><a href="{{ route('admin.users.index') }}" class="quick-link"><i class="bi bi-people"></i> {{ __('messages.users') }}</a></div>
</div>
<!-- Recent Orders + Low Stock -->
<div class="row g-3">
    <div class="col-md-7">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0">{{ __('messages.recent_orders') }}</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">{{ __('messages.view_all') }}</a>
            </div>
            <table class="table table-custom table-sm">
                <thead><tr><th>{{ __('messages.order_no') }}</th><th>{{ __('messages.customer') }}</th><th>{{ __('messages.total') }}</th><th>{{ __('messages.status') }}</th><th>{{ __('messages.date') }}</th></tr></thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="text-decoration-none fw-semibold">{{ $order->order_no }}</a></td>
                        <td>{{ $order->user->name }}</td>
                        <td class="fw-semibold">${{ number_format($order->total, 2) }}</td>
                        <td><span class="order-status-badge badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'cancelled' ? 'danger' : ($order->status == 'shipped' ? 'info' : 'warning')) }}">{{ __('messages.'.$order->status) }}</span></td>
                        <td class="text-muted small">{{ $order->created_at->format('M d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-5">
        <div class="admin-card">
            <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-exclamation-triangle"></i> {{ __('messages.low_stock_alert') }}</h6>
            @if($lowStockProducts->count())
                @foreach($lowStockProducts as $p)
                <div class="low-stock-item">
                    <div>
                        <div class="fw-semibold small">{{ $p->name ?? $p->sku }}</div>
                        <div class="text-muted small">{{ $p->category->name ?? '' }}</div>
                    </div>
                    <span class="badge bg-warning text-dark">{{ $p->stock }} {{ __('messages.left') }}</span>
                </div>
                @endforeach
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
                    <p class="mt-2">{{ __('messages.all_products_sufficient') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesData = @json($salesTrend->map(fn($s) => ['label' => date('M d', strtotime($s->date)), 'value' => (float)$s->revenue])->values());

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: salesData.map(d => d.label),
        datasets: [{
            label: 'Revenue',
            data: salesData.map(d => d.value),
            borderColor: '#667eea',
            fill: true,
            backgroundColor: 'rgba(102,126,234,0.1)',
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#667eea'
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } } }
});

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = @json($statusDist);

new Chart(statusCtx, {
    type: 'doughnut',
    data: { labels: Object.keys(statusData), datasets: [{ data: Object.values(statusData), backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545', '#6610f2'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

const catCtx = document.getElementById('catChart').getContext('2d');
const catData = @json($catDist->map(fn($c) => ['label' => $c->cat, 'value' => $c->cnt])->values());
new Chart(catCtx, {
    type: 'bar',
    data: { labels: catData.map(d => d.label), datasets: [{ label: 'Products', data: catData.map(d => d.value), backgroundColor: ['#667eea','#764ba2','#11998e','#38ef7d','#f12711','#f5af19'], borderRadius: 6 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});
</script>
@endpush
@endsection
