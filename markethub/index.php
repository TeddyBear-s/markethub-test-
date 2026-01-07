<?php
session_start();
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

require 'config.php';
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;
$page = isset($_GET['page']) ? max(1,intval($_GET['page'])) : 1;
$perpage = 6;
$params = [];
$where = [];
if ($cat_id > 0) { $where[] = 'p.category_id = ?'; $params[] = $cat_id; }
if ($q !== '') { $where[] = '(p.name LIKE ? OR p.description LIKE ?)'; $params[] = '%'.$q.'%'; $params[] = '%'.$q.'%'; }
$where_sql = count($where) ? 'WHERE '.implode(' AND ', $where) : '';
$cats = [];
$res = $mysqli->query("SELECT id, name FROM categories ORDER BY id");
while ($r = $res->fetch_assoc()) $cats[] = $r;
$total_sql = "SELECT COUNT(*) as cnt FROM products p $where_sql";
if (count($params)) {
    $stmt = $mysqli->prepare($total_sql);
    $types = '';
    foreach ($params as $p) $types .= is_int($p)?'i':'s';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $tr = $stmt->get_result()->fetch_assoc();
    $total = $tr['cnt'];
} else {
    $tr = $mysqli->query($total_sql)->fetch_assoc();
    $total = $tr['cnt'];
}
$pages = max(1, ceil($total/$perpage));
$start = ($page-1)*$perpage;
$products = [];
$sql = "SELECT p.* FROM products p $where_sql ORDER BY p.id DESC LIMIT ?,?";
if (count($params)) {
    $params2 = $params;
    $params2[] = $start; $params2[] = $perpage;
    $types = '';
    foreach ($params2 as $p) $types .= is_int($p)?'i':'s';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params2);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $start, $perpage);
    $stmt->execute();
    $res = $stmt->get_result();
}
while ($p = $res->fetch_assoc()) $products[] = $p;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>MarketHUB</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <div class="logo">MarketHUB</div>
  <nav>
    <a href="index.php">All</a>
    <?php foreach ($cats as $c): ?>
      <a href="?cat=<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></a>
    <?php endforeach; ?>
  </nav>
  <div class="actions">
    <form class="search" method="get">
      <input name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($q); ?>" />
      <?php if($cat_id): ?><input type="hidden" name="cat" value="<?php echo $cat_id; ?>"><?php endif; ?>
      <button type="submit">Search</button>
    </form>

    <?php if(isset($_SESSION['user_id'])): ?>
      <a class="link" href="profile.php">
        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
      </a>
      <a class="link" href="?logout=1" style="background: #ef4444;">Logout</a>
    <?php else: ?>
      <a href="login.html" class="link">Login / Register</a>
    <?php endif; ?>

    <button id="cartBtn" class="cart-link" type="button">
      <svg class="cart-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M7 4h-2l-1 2h2l3.6 7.59-1.35 2.45c-.16.29-.25.61-.25.96 0 1.1.9 2 2 2h9v-2h-9l1.1-2h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49-1.74-1.01-3.58 6.49h-7.45l-1.1-2h8.6v-2h-9.9l-1-2h-2z"/></svg>
      <span id="cartCount"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
    </button>
</div>
</header>

<main>
  <h2>Products</h2>
  <div class="products">
    <?php foreach ($products as $prod): ?>
      <div class="product">
        <img src="<?php echo htmlspecialchars($prod['image']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>">
        <h3><?php echo htmlspecialchars($prod['name']); ?></h3>
        <p><?php echo htmlspecialchars($prod['description']); ?></p>
        <div class="price">Rp <?php echo number_format($prod['price'],0,',','.'); ?></div>
        <button class="add-to-cart" type="button" data-id="<?php echo $prod['id']; ?>">Masukan dalam keranjang</button>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="pagination">
    <?php for($i=1;$i<=$pages;$i++): ?>
      <a class="page<?php if($i==$page) echo ' active'; ?>" href="?<?php echo http_build_query(array_merge($_GET, ['page'=>$i])); ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
  </div>
</main>

<div id="authModal" class="modal" aria-hidden="true">
  <div class="modal-content" role="dialog" aria-modal="true">
    <button class="close" id="closeModal" type="button">&times;</button>
    <div class="tabs">
      <button id="tabLogin" type="button">Login</button>
      <button id="tabRegister" type="button">Register</button>
    </div>
    <div id="loginForm" class="form">
      <input type="email" id="loginEmail" placeholder="Email" />
      <input type="password" id="loginPassword" placeholder="Password" />
      <div style="display:flex;gap:8px;"><button id="doLogin" type="button">Login</button></div>
      <div id="loginMsg" aria-live="polite"></div>
    </div>
    <div id="registerForm" class="form" style="display:none;">
      <input type="text" id="regName" placeholder="Full Name" />
      <input type="email" id="regEmail" placeholder="Email" />
      <input type="password" id="regPassword" placeholder="Password" />
      <div style="display:flex;gap:8px;"><button id="doRegister" type="button">Register</button></div>
      <div id="regMsg" aria-live="polite"></div>
    </div>
  </div>
</div>

<div id="cartModal" class="modal" aria-hidden="true" style="display:none;">
  <div class="cart-content">
    <button class="close" id="closeCartModal" type="button">&times;</button>
    <div id="cartBody">Loading...</div>
  </div>
</div>

<section id="testimonials" class="testimonials">
        <h2>What Our Users Say</h2>
        <div class="testimonial-slider">
            <button class="slider-btn prev" onclick="prevTestimonial()">&#10094;</button>
            <div class="testimonial active">
                <p>"MarketHub made buying online so easy and secure. Highly recommend!"</p>
                <cite>- Sarah J.</cite>
            </div>
            <div class="testimonial">
                <p>"Great selection and fast delivery. My go-to marketplace."</p>
                <cite>- Mike T.</cite>
            </div>
            <div class="testimonial">
                <p>"Selling my items was a breeze. The platform is user-friendly."</p>
                <cite>- Emily R.</cite>
            </div>
            <button class="slider-btn next" onclick="nextTestimonial()">&#10095;</button>
        </div>
    </section>

<footer id="contact">
        <p>&copy; 2023 MarketHUB. All rights reserved.</p>
        <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a> | <a href="#">Contact Us</a></p>
    </footer>

<script>
(function(){
  const loginBtn = document.getElementById('loginBtn');
  const authModal = document.getElementById('authModal');
  const closeModal = document.getElementById('closeModal');
  const tabLogin = document.getElementById('tabLogin');
  const tabRegister = document.getElementById('tabRegister');
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  if (loginBtn) loginBtn.addEventListener('click', ()=>{ authModal.style.display='flex'; authModal.setAttribute('aria-hidden','false'); });
  closeModal.addEventListener('click', ()=>{ authModal.style.display='none'; authModal.setAttribute('aria-hidden','true'); });
  window.addEventListener('click', (e)=>{ if (e.target===authModal) { authModal.style.display='none'; authModal.setAttribute('aria-hidden','true'); } });
  tabLogin.addEventListener('click', ()=>{ loginForm.style.display='block'; registerForm.style.display='none'; });
  tabRegister.addEventListener('click', ()=>{ loginForm.style.display='none'; registerForm.style.display='block'; });
  document.getElementById('doRegister').addEventListener('click', ()=>{
    const name=document.getElementById('regName').value.trim();
    const email=document.getElementById('regEmail').value.trim();
    const pass=document.getElementById('regPassword').value;
    document.getElementById('regMsg').innerText='';
    if(!name||!email||!pass){ document.getElementById('regMsg').innerText='Semua field harus diisi'; return; }
    fetch('auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'register',name,email,password:pass})}).then(r=>r.json()).then(j=>{ document.getElementById('regMsg').innerText=j.message; if(j.success) location.reload(); });
  });
  document.getElementById('doLogin').addEventListener('click', ()=>{
    const email=document.getElementById('loginEmail').value.trim();
    const pass=document.getElementById('loginPassword').value;
    document.getElementById('loginMsg').innerText='';
    if(!email||!pass){ document.getElementById('loginMsg').innerText='Isi email dan password'; return; }
    fetch('auth.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'login',email,password:pass})}).then(r=>r.json()).then(j=>{ document.getElementById('loginMsg').innerText=j.message; if(j.success) location.reload(); });
  });
})();

// JavaScript testimonial
        let currentTestimonial = 0;
        const testimonials = document.querySelectorAll('.testimonial');
        function showTestimonial(index) {
            testimonials.forEach((testimonial, i) => {
                testimonial.classList.toggle('active', i === index);
            });
        }
        
        function nextTestimonial() {
            currentTestimonial = (currentTestimonial + 1) % testimonials.length;
            showTestimonial(currentTestimonial);
        }
        
        function prevTestimonial() {
            currentTestimonial = (currentTestimonial - 1 + testimonials.length) % testimonials.length;
            showTestimonial(currentTestimonial);
        }
        
        setInterval(nextTestimonial, 5000);

(function(){
  const cartBtn = document.getElementById('cartBtn');
  const cartModal = document.getElementById('cartModal');
  const closeCartModal = document.getElementById('closeCartModal');
  function openCart(){ cartModal.style.display='flex'; cartModal.setAttribute('aria-hidden','false'); document.body.style.overflow='hidden'; fetch('cart_widget.php').then(r=>r.text()).then(html=>{ document.getElementById('cartBody').innerHTML=html; hookCartButtons(); updateCartCount(); }); }
  function closeCart(){ cartModal.style.display='none'; cartModal.setAttribute('aria-hidden','true'); document.body.style.overflow='auto'; }
  if(cartBtn) cartBtn.addEventListener('click', openCart);
  closeCartModal.addEventListener('click', closeCart);
  window.addEventListener('click', (e)=>{ if(e.target===cartModal) closeCart(); });
  function hookCartButtons(){
    document.querySelectorAll('.inc').forEach(b=>b.addEventListener('click', ()=>{ const id=b.dataset.id; const qty=parseInt(document.getElementById('qty-'+id).innerText)||0; fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'update',product_id:id,qty:qty+1})}).then(()=>openCart()); }));
    document.querySelectorAll('.dec').forEach(b=>b.addEventListener('click', ()=>{ const id=b.dataset.id; const qty=parseInt(document.getElementById('qty-'+id).innerText)||0; if(qty-1<1){ fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'remove',product_id:id})}).then(()=>openCart()); } else { fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'update',product_id:id,qty:qty-1})}).then(()=>openCart()); } }));
    document.querySelectorAll('.remove').forEach(b=>b.addEventListener('click', ()=>{ const id=b.dataset.id; fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'remove',product_id:id})}).then(()=>openCart()); }));
    document.getElementById('clearCart')?.addEventListener('click', ()=>{ fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'clear'})}).then(()=>openCart()); });
  }
  function updateCartCount(){ fetch('cart_count.php').then(r=>r.json()).then(j=>{ const el=document.getElementById('cartCount'); if(el) el.innerText=j.count; }); }
  updateCartCount();
  document.querySelectorAll('.add-to-cart').forEach(btn=>{ btn.addEventListener('click', ()=>{ const id = btn.getAttribute('data-id'); fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({action:'add',product_id:id})}).then(r=>r.json()).then(j=>{ updateCartCount(); if(j.success){ openCart(); } else { alert(j.message); } }); }); });
})();
</script>
</body>
</html>
