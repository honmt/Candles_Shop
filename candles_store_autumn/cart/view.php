<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$cart = $_SESSION['cart'] ?? [];

/* Обработка действий */
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];

    if (isset($cart[$id])) {
        if ($_GET['action'] === 'plus') {
            $cart[$id]++;
        }
        elseif ($_GET['action'] === 'minus') {
            $cart[$id]--;
            if ($cart[$id] <= 0) {
                unset($cart[$id]);
            }
        }
        elseif ($_GET['action'] === 'remove') {
            unset($cart[$id]);
        }
    }

    $_SESSION['cart'] = $cart;
    header('Location: view.php');
    exit;
}

/* Получаем товары */
$items = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");

    while ($row = $res->fetch_assoc()) {
        $row['qty'] = $cart[$row['id']];
        $row['sum'] = $row['qty'] * $row['price'];
        $total += $row['sum'];
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Корзина</title>
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
      <a href="view.php" class="nav-link accent">Корзина</a>
    </nav>
  </div>
</header>

<main class="container">

<h2 class="section-title">Ваша корзина</h2>

<?php if (empty($items)): ?>
  <div class="empty-box">
    <p>🕯 Ваша корзина пуста</p>
    <a href="../catalog.php" class="btn accent">Перейти в каталог</a>
  </div>
<?php else: ?>

<div class="cart-list">

<?php foreach ($items as $it): ?>
  <div class="cart-item">

    <div class="cart-info">
      <h3><?= htmlspecialchars($it['name']) ?></h3>

      <div class="cart-controls">
        <a href="?action=minus&id=<?= $it['id'] ?>" class="qty-btn">−</a>
        <span class="qty"><?= $it['qty'] ?></span>
        <a href="?action=plus&id=<?= $it['id'] ?>" class="qty-btn">+</a>
      </div>

      <a href="?action=remove&id=<?= $it['id'] ?>" class="remove-link">
        Удалить
      </a>
    </div>

    <div class="cart-sum">
      <?= number_format($it['sum'],2,'.',',') ?> ₽
    </div>

  </div>
<?php endforeach; ?>

<div class="cart-total">
  <span>Итого:</span>
  <strong><?= number_format($total,2,'.',',') ?> ₽</strong>
</div>

<div class="cart-actions">
    <a href="../user/payment.php" class="btn accent">Перейти к оплате</a>
</div>

</div>
<?php endif; ?>

</main>

<footer class="site-footer">
<p>© <?=date("Y")?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
