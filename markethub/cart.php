<?php
session_start();
require 'config.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
$total = 0;
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");
    while ($p = $res->fetch_assoc()) {
        $p['qty'] = $cart[$p['id']];
        $p['subtotal'] = $p['qty'] * $p['price'];
        $total += $p['subtotal'];
        $products[] = $p;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cart - MarketHUB</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <div class="logo">MarketHUB</div>
    <nav><a href="index.php">Home</a></nav>
</header>
<main>
    <h2>Keranjang</h2>
    <?php if (empty($products)): ?>
        <p>Keranjang kosong</p>
    <?php else: ?>
        <table class="cart-table">
            <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td>Rp <?php echo number_format($p['price'],0,',','.'); ?></td>
                    <td>
                        <button class="dec" data-id="<?php echo $p['id']; ?>">-</button>
                        <span class="qty" id="qty-<?php echo $p['id']; ?>"><?php echo $p['qty']; ?></span>
                        <button class="inc" data-id="<?php echo $p['id']; ?>">+</button>
                    </td>
                    <td>Rp <?php echo number_format($p['subtotal'],0,',','.'); ?></td>
                    <td><button class="remove" data-id="<?php echo $p['id']; ?>">Remove</button></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <h3>Total: Rp <?php echo number_format($total,0,',','.'); ?></h3>
        <a href="checkout.php" class="checkout">Proceed to Checkout</a>
        <button id="clearCart">Clear Cart</button>
    <?php endif; ?>
</main>
<script>
document.querySelectorAll('.inc').forEach(b=>b.onclick = ()=>{ const id=b.getAttribute('data-id'); update(id,1); });
document.querySelectorAll('.dec').forEach(b=>b.onclick = ()=>{ const id=b.getAttribute('data-id'); update(id,-1); });
document.querySelectorAll('.remove').forEach(b=>b.onclick = ()=>{ const id=b.getAttribute('data-id'); fetch('cart_action.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'remove', product_id:id})}).then(()=>location.reload()); });
document.getElementById('clearCart')?.addEventListener('click', ()=>{ fetch('cart_action.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'clear'})}).then(()=>location.reload()); });
function update(id, delta){ const span = document.getElementById('qty-'+id); let qty = parseInt(span.innerText) + delta; if (qty < 1) { fetch('cart_action.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'remove', product_id:id})}).then(()=>location.reload()); return; } fetch('cart_action.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'update', product_id:id, qty})}).then(()=>location.reload()); }
</script>
</body>
</html>
