<!-- Sidebar Category Navigation -->
<div class="crmb-sidebar-cat d-none d-lg-block">
    <div class="sidebar-header">
        <i class="bi bi-grid-3x3-gap-fill"></i> All Categories
    </div>
    @foreach($categories as $cat)
    <a href="{{ route('category', $cat->slug) }}" class="sidebar-item">
        <i class="bi {{ ['bi-laptop','bi-phone','bi-watch','bi-headphones','bi-camera','bi-controller','bi-house','bi-flower1','bi-bicycle','bi-heart-pulse','bi-gem','bi-bag','bi-cpu','bi-display','bi-motherboard','bi-printer','bi-usb-drive'][$loop->index % 17] }}"></i>
        <span>{{ $cat->name }}</span>
        @if($cat->children->count() > 0)
        <span class="badge-new">{{ $cat->children->count() }}</span>
        @endif
    </a>
    @endforeach
</div>