<?php
$f = 'resources/views/admin/dashboard.blade.php';
$c = file_get_contents($f);

// 1. Fix stat cards row - add 3 new cards
$oldStats = '<div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-pink">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-label">Total Users</div>
            <div class="stat-num">{{ $totalUsers }}</div>
        </div>
    </div>
</div>';

$newStats = '<div class="col-md-3 col-6">
        <div class="admin-stat-card gradient-pink">
            <i class="bi bi-people stat-icon"></i>
            <div class="stat-label">Total Users</div>
            <div class="stat-num">{{ $totalUsers }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#fa709a,#fee140);color:#fff;">
            <i class="bi bi-star stat-icon"></i>
            <div class="stat-label">Reviews</div>
            <div class="stat-num">{{ $totalReviews ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#30cfd0,#330867);color:#fff;">
            <i class="bi bibi-star-fill stat-icon"></i>
            <div class="stat-label">Avg Rating</div>
            <div class="stat-num">{{ $avgRating ?? 0 }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="admin-stat-card" style="background:linear-gradient(135deg,#ee5a24,#f9ca24);color:#fff;">
            <i class="bi bi-exclamation-triangle stat-icon"></i>
            <div class="stat-label">Low Stock</div>
            <div class="stat-num">{{ $lowStockCount ?? 0 }}</div>
        </div>
    </div>
</div>';

$c = str_replace($oldStats, $newStats, $c);

// 2. Add Top Products + Category section before Quick Links
$topSection = '
<!-- Top Products + Category Distribution -->
<div class="row g-3 mb-4">
    <div class="col-md-7">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-trophy text-warning"></i> Top Selling Products</h6>
            </div>
            <table class="table table-custom table-sm">
                <thead><tr><th>#</th><th>Product</th><th>Sold</th><th>Stock</th><th>Revenue</th></tr></thead>
                <tbody>
                    @foreach($topProducts as $p)
                    <tr>
                        <td><span class="badge bg-primary">{{ $loop->index + 1 }}</span></td>
                        <td class="fw-semibold small">{{ $p->name ?? $p->sku }}</td>
                        <td class="fw-bold text-success">{{ $p->sold ?? 0 }}</td>
                        <td><span class="badge {{ ($p->stock ?? 0) <= ($p->low_stock_threshold ?? 5) ? "bg-danger" : "bg-success" }}">{{ $p->stock }}</span></td>
                        <td class="fw-semibold">${{ number_format(($p->price ?? 0) * ($p->sold ?? 0), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-5">
        <div class="admin-card">
            <h6 class="fw-bold mb-3"><i class="bi bi-tags text-primary"></i> Category Distribution</h6>
            <canvas id="catChart" height="180"></canvas>
        </div>
    </div>
</div>

';

$c = str_replace('<!-- Quick Links -->', $topSection . '<!-- Quick Links -->', $c);

// 3. Update sales chart to use salesTrend data
$c = str_replace(
    "const salesData = @json(\$recentOrders->pluck('total', 'created_at')->map(function(\$v, \$k) { return ['label' => \$k->format('M d'), 'value' => \$v]; })->values());",
    "const salesData = @json(\$salesTrend->map(fn(\$s) => ['label' => date('M d', strtotime(\$s->date)), 'value' => (float)\$s->revenue])->values());",
    $c
);

// 4. Update status chart data source
$c = str_replace(
    "const statusData = @json(\$recentOrders->groupBy('status')->map(fn(\$g) => \$g->count()));",
    "const statusData = @json(\$statusDist);",
    $c
);

// 5. Add category chart script
$c = str_replace(
    "</script>\n@endpush",
    "// Category Distribution Chart\nconst catCtx = document.getElementById('catChart').getContext('2d');\nconst catData = @json(\$catDist->map(fn(\$c) => ['label' => \$c->cat, 'value' => \$c->cnt])->values());\nnew Chart(catCtx, {\n    type: 'bar',\n    data: {\n        labels: catData.map(d => d.label),\n        datasets: [{\n            label: 'Products',\n            data: catData.map(d => d.value),\n            backgroundColor: ['#667eea','#764ba2','#11998e','#38ef7d','#f12711','#f5af19'],\n            borderRadius: 6\n        }]\n    },\n    options: {\n        responsive: true,\n        plugins: { legend: { display: false } },\n        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }\n    }\n});\n</script>\n@endpush",
    $c
);

file_put_contents($f, $c);
echo "Dashboard view upgraded successfully\n";
