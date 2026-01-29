<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Интернет-магазин свечей — Осенний</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
  <div class="header-inner">

    <div class="logo">
      <span>🕯</span> Осенний магазин свечей
    </div>

    <nav class="main-nav">
      <a href="index.php" class="nav-link">Главная</a>
      <a href="catalog.php" class="nav-link">Каталог</a>

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

      <a href="cart/view.php" class="cart-btn">
        🛒 Корзина
      </a>
    </nav>

  </div>
</header>
<main class="container">

  <!-- HERO -->
  <section class="hero">
  <div class="hero-content">
    <h2>Интернет-магазин соевых свечей</h2>
    <p>
      Натуральные ароматические свечи ручной работы.
      Уют, тепло и атмосфера спокойствия в каждом доме.
    </p>
    <a class="btn accent" href="catalog.php">Перейти в каталог</a>
  </div>
</section>


  <!-- О МАГАЗИНЕ -->
  <section style="margin-bottom:70px;">
    <h3 style="font-size:26px;margin-bottom:15px;">Почему именно наши свечи</h3>
    <p>
      Осенний магазин свечей - это сочетание минимализма, натуральных материалов
      и тщательно подобранных ароматов. Мы используем только экологичный соевый воск,
      качественные фитили и безопасные ароматические масла.
    </p>
    <p>
      Наши свечи подойдут для отдыха, медитации, уютных вечеров и в качестве подарка.
    </p>
  </section>

  <!-- КАТЕГОРИИ -->
  <section class="categories">
  <h3 class="section-title">Категории товаров</h3>

  <div class="categories-grid">

    <div class="category-card" style="background-image:url('assets/images/form.jpg')">
      <div class="category-overlay">
        <h4>Формовые свечи</h4>
        <p>Декоративные свечи для интерьера</p>
        <a href="catalog.php" class="cat-btn">Смотреть</a>
      </div>
    </div>

    <div class="category-card" style="background-image:url('assets/images/jar.jpg')">
      <div class="category-overlay">
        <h4>Контейнерные свечи</h4>
        <p>Свечи в стильных банках</p>
        <a href="catalog.php" class="cat-btn">Смотреть</a>
      </div>
    </div>

    <div class="category-card" style="background-image:url('assets/images/gift.jpg')">
      <div class="category-overlay">
        <h4>Подарочные наборы</h4>
        <p>Идеальный подарок для близких</p>
        <a href="catalog.php" class="cat-btn">Смотреть</a>
      </div>
    </div>

  </div>
</section>

  <!-- ПРЕИМУЩЕСТВА -->
  <section style="margin:80px 0;">
    <h3 style="font-size:26px;margin-bottom:20px;">Наши преимущества</h3>
    <div class="featured">
      <div class="card">🌱 Натуральный соевый воск</div>
      <div class="card">🕯 Ручная работа</div>
      <div class="card">🎁 Подходит для подарка</div>
      <div class="card">🍂 Осенняя уютная атмосфера</div>
    </div>
  </section>

  <!-- АКЦИЯ -->
  <section class="card" style="text-align:center;">
    <h3 style="font-size:28px;">Скидка 25%</h3>
    <p>При заказе от 10 свечей — отличное решение для подарков и мероприятий.</p>
    <a class="btn" href="catalog.php">Выбрать свечи</a>
  </section>

</main>

<footer class="site-footer">
  <p>© <?=date("Y")?> Осенний магазин свечей</p>
</footer>
</body>
</html>
