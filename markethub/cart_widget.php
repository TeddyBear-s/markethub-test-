<?php
session_start();
require 'config.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
if (empty($cart)) { echo '<p>Keranjang kosong.</p>'; exit; }
$ids = implode(',', array_map('intval', array_keys($cart)));
$res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");
$items = array();
$total = 0;
while ($p = $res->fetch_assoc()) {
    $p['qty'] = isset($cart[$p['id']]) ? $cart[$p['id']] : 0;
    $p['subtotal'] = $p['qty'] * $p['price'];
    $total += $p['subtotal'];
    $items[] = $p;
}
?>
<div>
  <h3>Keranjang</h3>
  <?php foreach ($items as $it): ?>
    <div class="cart-item">
      <img src="<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['name']); ?>">
      <div style="flex:1;">
        <div style="font-weight:700"><?php echo htmlspecialchars($it['name']); ?></div>
        <div style="color:#6b7280">Rp <?php echo number_format($it['price'],0,',','.'); ?></div>
        <div class="cart-actions">
          <button class="dec" data-id="<?php echo $it['id']; ?>" type="button">-</button>
          <span id="qty-<?php echo $it['id']; ?>"><?php echo $it['qty']; ?></span>
          <button class="inc" data-id="<?php echo $it['id']; ?>" type="button">+</button>
          <button class="remove" data-id="<?php echo $it['id']; ?>" type="button">Remove</button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <hr>
  <div style="font-weight:800">Total: Rp <?php echo number_format($total,0,',','.'); ?></div>
  <div style="margin-top:12px;display:flex;gap:8px">
    <a href="checkout.php" class="link">Checkout</a>
    <button id="clearCart" class="clear-btn" type="button">Hapus Keranjang</button>
  </div>
</div>
