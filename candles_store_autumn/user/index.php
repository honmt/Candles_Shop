<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];

/* Заказы пользователя */
$orders = [];
$res = $mysqli->query("
    SELECT * 
    FROM orders
    WHERE user_id = $user_id
    ORDER BY created_at DESC
");

while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Личный кабинет</title>
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
      <a href="index.php" class="nav-link accent">Мой кабинет</a>
      <a href="profile.php" class="active">Профиль</a>
      <a href="../cart/view.php" class="nav-link">Корзина</a>
      <a href="../auth/logout.php" class="nav-link danger">
        Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
      </a>
    </nav>
  </div>
</header>

<main class="container">

  <h2 class="section-title">
    Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['username']) ?> 🌿
  </h2>

  <section class="profile-card">
    <p><strong>Роль:</strong> Покупатель</p>
    <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['user']['email'] ?? '—') ?></p>
    <a href="profile.php" class="btn small">Редактировать профиль</a>
  </section>

  <h2 class="section-title">Мои заказы</h2>

  <?php if (empty($orders)): ?>
    <p>Вы ещё не оформили ни одного заказа.</p>
  <?php else: ?>

  <div class="orders-table-wrapper">
    <table class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Дата</th>
          <th>Сумма</th>
          <th>Статус</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= intval($o['id']) ?></td>
            <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
            <td><?= number_format($o['total'], 2, '.', ',') ?> ₽</td>
            <td>
              <span class="status <?= htmlspecialchars($o['status']) ?>">
                <?= htmlspecialchars($o['status']) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <?php endif; ?>

</main>

<footer class="site-footer">
  <p>© <?= date('Y') ?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
