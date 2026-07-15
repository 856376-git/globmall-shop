<?php
// 在首页的hero banner后面插入侧边分类栏
$file = file_get_contents('resources/views/home.blade.php');

// 在分类网格前加入侧边栏布局包裹
$old = '<!-- ===== CATEGORY GRID =====';
$new = '<!-- ===== CATEGORY SIDEBAR + CONTENT ===== -->
<div class="row mb-4">
    <div class="col-lg-2 d-none d-lg-block">
        @include(\'partials.sidebar-categories\')
    </div>
    <div class="col-lg-10">

<!-- ===== CATEGORY GRID =====';

$file = str_replace($old, $new, $file, $count1);

// 在品牌展示后关闭侧边栏的col-lg-10和row
$old2 = "<!-- ===== FLOATING TOOLS =====";
$new2 = "    </div><!-- end col-lg-10 -->
</div><!-- end row -->

<!-- ===== FLOATING TOOLS =====";

$file = str_replace($old2, $new2, $file, $count2);

if ($count1 && $count2) {
    file_put_contents('resources/views/home.blade.php', $file, LOCK_EX);
    echo "Sidebar layout added to home page!\n";
} else {
    echo "WARNING: Could not find insertion points (count1=$count1, count2=$count2)\n";
}
