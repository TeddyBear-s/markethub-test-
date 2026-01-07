<?php
session_start();
require 'config.php';

$is_logged_in = isset($_SESSION['user_id']);
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) { header('Location: index.php'); exit; }
$ids = implode(',', array_map('intval', array_keys($cart)));
$res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");
$products = []; $total = 0;
while ($p = $res->fetch_assoc()) { 
    $p['qty'] = $cart[$p['id']]; 
    $p['subtotal'] = $p['qty'] * $p['price']; 
    $total += $p['subtotal']; 
    $products[] = $p; 
}

if ($is_logged_in && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $mysqli->real_escape_string($_POST['ship_name']);
    $addr = $mysqli->real_escape_string($_POST['ship_address']);
    $user_id = $_SESSION['user_id'];
    $stmt = $mysqli->prepare('INSERT INTO orders (user_id,total,shipping_name,shipping_address) VALUES (?,?,?,?)');
    $stmt->bind_param('idss', $user_id, $total, $name, $addr);
    if ($stmt->execute()) {
        $order_id = $mysqli->insert_id;
        $stmt2 = $mysqli->prepare('INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)');
        foreach ($products as $p) { 
            $stmt2->bind_param('iiii', $order_id, $p['id'], $p['qty'], $p['price']); 
            $stmt2->execute(); 
        }
        $_SESSION['cart'] = [];
        header('Location: profile.php?order=ok'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - MarketHUB</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    :root {
        --primary: #2563eb;
        --bg: #f8fafc;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--bg);
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
    }

    .card {
        background: white;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .login-warning {
        text-align: center;
        padding: 40px 20px;
    }

    .warning-icon {
        font-size: 50px;
        margin-bottom: 20px;
        display: block;
    }

    .btn-login-now {
        display: inline-block;
        background: var(--primary);
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        margin-top: 20px;
        transition: 0.3s;
    }

    .btn-login-now:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
    }

    .grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    input,
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-sizing: border-box;
    }

    .btn-pay {
        width: 100%;
        background: var(--primary);
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>

<div class="container">
    <header style="margin-bottom: 20px;">
        <a href="index.php" style="text-decoration:none; color: var(--primary); font-weight:600;">← Kembali Belanja</a>
    </header>

    <?php if (!$is_logged_in): ?>
        <div class="card login-warning">
            <span class="warning-icon">🔐</span>
            <h2 style="margin:0">Ups! Anda Belum Login</h2>
            <p style="color: #64748b; margin-top: 10px;">Silakan login terlebih dahulu untuk melanjutkan proses pembayaran dan pengiriman barang.</p>
            <a href="login.html" class="btn-login-now">Login Sekarang</a>
        </div>
    <?php else: ?>
        <div class="grid">
            <div class="card">
                <h3 style="margin-top:0">Informasi Pengiriman</h3>
                <form id="checkoutForm" method="POST">
                    <div class="form-group">
                        <label>Nama Penerima</label>
                        <input name="ship_name" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Lengkap</label>
                        <textarea name="ship_address" rows="4" placeholder="Alamat jalan, nomor rumah..." required></textarea>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3 style="margin-top:0">Ringkasan</h3>
                <?php foreach ($products as $p): ?>
                    <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:8px;">
                        <span><?php echo $p['name']; ?> (x<?php echo $p['qty']; ?>)</span>
                        <span>Rp<?php echo number_format($p['subtotal'],0,',','.'); ?></span>
                    </div>
                <?php endforeach; ?>
                <hr style="border:0; border-top:1px solid #eee; margin:15px 0;">
                <div style="display:flex; justify-content:space-between; font-weight:700; color:var(--primary);">
                    <span>Total</span>
                    <span>Rp<?php echo number_format($total,0,',','.'); ?></span>
                </div>
                <button type="submit" form="checkoutForm" class="btn-pay">Bayar Sekarang</button>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>