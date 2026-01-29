<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Получаем все заказы
$orders = [];
$sql = "
    SELECT o.*, u.username 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
";
$res = $mysqli->query($sql);
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Заказы — Админка</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header class="site-header">
  <h1>Админ-панель</h1>
  <nav>
    <a href="../index.php">Главная</a>
    <a href="../catalog.php">Каталог</a>
    <a href="index.php">Админка</a>
    <a href="users.php">Пользователи</a>
    <a href="products.php" class="active">Товары</a>
    <a href="../auth/logout.php">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>

<main class="container">

  <h2 class="section-title">Все заказы</h2>

  <?php if (empty($orders)): ?>
    <p>Заказов пока нет.</p>
  <?php else: ?>

  <div class="orders-table-wrapper">
    <table class="orders-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Пользователь</th>
          <th>Сумма</th>
          <th>Статус</th>
          <th>Дата</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td>#<?= intval($o['id']) ?></td>
            <td><?= htmlspecialchars($o['username'] ?? 'Гость') ?></td>
            <td><?= number_format($o['total_price'], 2, '.', ',') ?> ₽</td>
            <td>
              <span class="status <?= htmlspecialchars($o['status']) ?>">
                <?= htmlspecialchars($o['status']) ?>
              </span>
            </td>
            <td><?= date('d.m.Y H:i', strtotime($o['created_at'])) ?></td>
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

</body>
</html>
