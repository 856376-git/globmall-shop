<?php
$f = 'resources/views/account.blade.php';
$c = file_get_contents($f);

// Add recently viewed link in menu
$oldMenu = '<a href="{{ route(' . chr(39) . 'logout' . chr(39) . ') }}"';
$newMenu = '<a href="{{ route(' . chr(39) . 'recently-viewed' . chr(39) . ')"><i class="bi bi-clock-history"></i> Recently Viewed</a>
                <a href="{{ route(' . chr(39) . 'logout' . chr(39) . ') }}"';

if (strpos($c, 'Recently Viewed') === false) {
    $c = str_replace($oldMenu, $newMenu, $c);
}

// Add recently viewed section after Recent Orders
$oldOrders = '</div>
            @endif
        </div>';

$newOrders = '</div>
            @endif

            <!-- Recently Viewed -->
            <h5 class="fw-bold mb-3 mt-5">Recently Viewed</h5>
            @if($recentlyViewed && $recentlyViewed->count())
            <div class="row g-3">
                @foreach($recentlyViewed as $rProduct)
                <div class="col-6 col-md-4">
                    @include('partials.product-card', ['product' => $rProduct])
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-clock-history" style="font-size:3rem;"></i>
                <p class="mt-2">No recently viewed items</p>
                <a href="{{ route('shop') }}" class="btn btn-primary btn-sm">Browse Products</a>
            </div>
            @endif
        </div>';

if (strpos($c, 'Recently Viewed') === false) {
    $c = str_replace($oldOrders, $newOrders, $c);
}

file_put_contents($f, $c);
echo "Patched account.blade.php\n";
