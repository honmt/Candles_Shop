<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$order_id = intval($_GET['order_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Заказ оформлен</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="page">

<header class="site-header">
  <div class="header-inner">
    <div class="logo">🕯 Осенний магазин свечей</div>
    <nav class="main-nav">
      <a href="../index.php" class="nav-link">Главная</a>
      <a href="../catalog.php" class="nav-link">Каталог</a>
      <a href="index.php" class="nav-link">Кабинет</a>
      <a href="../cart/view.php" class="nav-link">Корзина</a>
    </nav>
  </div>
</header>

<main class="container">

  <div class="success-card">

    <div class="success-icon">✅</div>

    <h2 class="success-title">Заказ успешно оформлен</h2>

    <p class="success-text">
      Спасибо за покупку!  
      Ваш заказ <strong>№<?= $order_id ?></strong> принят и передан в обработку.
    </p>

    <div class="success-actions">
      <a href="index.php" class="btn accent">Мои заказы</a>
      <a href="../catalog.php" class="btn">Вернуться в каталог</a>
    </div>

  </div>

</main>

<footer class="site-footer">
  <p>© <?= date('Y') ?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
