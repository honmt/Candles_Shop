<?php
session_start();
require_once 'config/db.php';

$products = [];
$res = $mysqli->query("
    SELECT p.*, u.username AS seller_name 
    FROM products p 
    LEFT JOIN users u ON p.seller_id = u.id 
    ORDER BY p.created_at DESC
");
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Каталог свечей</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="page">

<header class="site-header">
  <div class="header-inner">

    <div class="logo">
      <span>🕯</span> Осенний магазин свечей
    </div>

    <nav class="main-nav">
      <a href="index.php" class="nav-link">Главная</a>
      <a href="catalog.php" class="nav-link accent">Каталог</a>

      <?php if(isset($_SESSION['user'])): ?>
        <?php if($_SESSION['user']['role']=='admin'): ?>
          <a href="admin/index.php" class="nav-link">Админ</a>
        <?php elseif($_SESSION['user']['role']=='seller'): ?>
          <a href="seller/index.php" class="nav-link">Продавец</a>
        <?php else: ?>
          <a href="user/index.php" class="nav-link">Кабинет</a>
        <?php endif; ?>
        <a href="auth/logout.php" class="nav-link muted">
          Выйти (<?=htmlspecialchars($_SESSION['user']['username'])?>)
        </a>
      <?php else: ?>
        <a href="auth/login.php" class="nav-link">Вход</a>
        <a href="auth/register.php" class="nav-link accent">Регистрация</a>
      <?php endif; ?>

      <a href="cart/view.php" class="cart-btn">🛒 Корзина</a>
    </nav>

  </div>
</header>

<main class="container">

  <h2 class="section-title">Каталог свечей</h2>

  <?php if(empty($products)): ?>
    <p>Каталог пуст. Администратор или продавцы могут добавить товары.</p>
  <?php else: ?>

  <div class="catalog-grid">
    <?php foreach($products as $p): ?>

      <?php
        $image = 'assets/images/product-placeholder.jpg';
        if (str_contains(mb_strtolower($p['name']), 'смород')) {
            $image = 'assets/images/currant.jpg';
        }
      ?>

      <div class="catalog-card">

  <div class="catalog-image">
    <img src="assets/images/currant.jpg" alt="<?=htmlspecialchars($p['name'])?>">
  </div>

  <div class="catalog-content">

    <h3 class="product-title">
      <?=htmlspecialchars($p['name'])?>
    </h3>

    <p class="catalog-desc">
      <?=nl2br(htmlspecialchars($p['description']))?>
    </p>

    <div class="catalog-info">
      <span class="price"><?=number_format($p['price'],2,'.',' ')?> ₽</span>
      <span class="qty">В наличии: <?=intval($p['quantity'])?></span>
    </div>

    <p class="catalog-seller">
      Продавец: <?=htmlspecialchars($p['seller_name'] ?? 'Магазин')?>
    </p>

    <?php if(isset($_SESSION['user']) && $_SESSION['user']['role']==='user'): ?>
      <form method="post" action="cart/add_to_cart.php" class="catalog-form">
        <input type="hidden" name="product_id" value="<?=intval($p['id'])?>">
        <input type="number" name="qty" value="1" min="1" max="<?=intval($p['quantity'])?>">
        <button class="btn accent">В корзину</button>
      </form>
    <?php else: ?>
      <p class="only-users">В корзину могут добавлять только покупатели</p>
    <?php endif; ?>

  </div>
</div>

      </div>
    <?php endforeach; ?>
  </div>

  <?php endif; ?>

</main>

<footer class="site-footer">
  <p>© <?=date("Y")?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
