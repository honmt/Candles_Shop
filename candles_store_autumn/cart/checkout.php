<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: view.php');
    exit;
}

/* Получаем товары */
$ids = implode(',', array_map('intval', array_keys($cart)));
$res = $mysqli->query("SELECT * FROM products WHERE id IN ($ids)");

$total = 0;
$products = [];

while ($r = $res->fetch_assoc()) {
    $r['qty'] = $cart[$r['id']];
    $r['sum'] = $r['qty'] * $r['price'];
    $total += $r['sum'];
    $products[] = $r;
}

/* Оформление заказа */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mysqli->begin_transaction();

    try {
        $status = 'processing';
        $stmt = $mysqli->prepare(
            "INSERT INTO orders (user_id, total, status) VALUES (?, ?, ?)"
        );
        $stmt->bind_param('ids', $_SESSION['user']['id'], $total, $status);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        $itemStmt = $mysqli->prepare(
            "INSERT INTO order_items (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)"
        );

        foreach ($products as $p) {
            $itemStmt->bind_param(
                'iidi',
                $order_id,
                $p['id'],
                $p['price'],
                $p['qty']
            );
            $itemStmt->execute();

            $mysqli->query(
                "UPDATE products 
                 SET quantity = quantity - " . intval($p['qty']) . "
                 WHERE id = " . intval($p['id'])
            );
        }

        $itemStmt->close();
        $mysqli->commit();

        unset($_SESSION['cart']);
        header('Location: ../user/order_success.php?order_id=' . $order_id);
        exit;

    } catch (Exception $e) {
        $mysqli->rollback();
        $error = 'Ошибка оформления заказа';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Подтверждение заказа</title>
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
      <header class="site-header">
    <a href="cart/view.php" class="cart-btn">
        🛒 Корзина
      </a>
    <a href="../auth/logout.php" class="danger">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>
    </nav>
  </div>
</header>

<main class="container">

  <h2 class="section-title">Подтверждение заказа</h2>

  <?php if (!empty($error)): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
  <?php endif; ?>

  <div class="checkout-box">

    <?php foreach ($products as $p): ?>
      <div class="checkout-item">
        <span><?= htmlspecialchars($p['name']) ?> × <?= $p['qty'] ?></span>
        <strong><?= number_format($p['sum'],2,'.',',') ?> ₽</strong>
      </div>
    <?php endforeach; ?>

    <div class="checkout-total">
      <span>Итого:</span>
      <strong><?= number_format($total,2,'.',',') ?> ₽</strong>
    </div>

    <form method="post" class="checkout-actions">
      <button type="submit" class="btn accent">
        Подтвердить заказ
      </button>
      <a href="view.php" class="btn muted">
        Вернуться в корзину
      </a>
    </form>

  </div>

</main>

<footer class="site-footer">
  <p>© <?=date("Y")?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
