<script>
// Update cart count every 5 seconds
function updateCartCount() {
    fetch('{{ route("cart.count") }}', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        const el = document.getElementById('cartCount');
        if (el) el.textContent = data.count;
    });
}
setInterval(updateCartCount, 5000);
</script>