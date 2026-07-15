<?php
// Patch account.blade.php - add recently viewed link in menu
$c = file_get_contents('resources/views/account.blade.php');

$oldLink = '<a href="{{ route(' . "'" . 'logout' . "'" . ') }}" onclick="event.preventDefault();';
$newLink = '<a href="{{ route(' . "'" . 'recently-viewed' . "'" . ') }}"><i class="bi bi-clock-history"></i> Recently Viewed</a>' . PHP_EOL . '                <a href="{{ route(' . "'" . 'logout' . "'" . ') }}" onclick="event.preventDefault();';

if (strpos($c, 'clock-history') === false) {
    $c = str_replace($oldLink, $newLink, $c);
}

file_put_contents('resources/views/account.blade.php', $c);
echo "Patched account page\n";
