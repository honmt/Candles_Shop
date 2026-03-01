<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'seller') {
    header('Location: ../auth/login.php');
    exit;
}

$seller_id = $_SESSION['user']['id'];

// Добавление товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['quantity']);

    if ($name !== '' && $price > 0) {
        $stmt = $mysqli->prepare(
            "INSERT INTO products (seller_id, name, description, price, quantity)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('issdi', $seller_id, $name, $desc, $price, $qty);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit;
}

// Удаление товара
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $mysqli->query("DELETE FROM products WHERE id=$id AND seller_id=$seller_id");
    header('Location: index.php');
    exit;
}

// Товары продавца
$products = [];
$res = $mysqli->query("
    SELECT * FROM products
    WHERE seller_id = $seller_id
    ORDER BY id DESC
");
while ($row = $res->fetch_assoc()) {
    $products[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Кабинет продавца</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header class="site-header">
  <h1>Кабинет продавца</h1>
  <nav>
    <a href="../index.php">Главная</a>
    <a href="../catalog.php">Каталог</a>
    <a href="index.php" class="active">Продавец</a>
    <a href="profile.php">Профиль</a>
    <a href="../auth/logout.php">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>

<main class="container">

  <h2 class="section-title">Добавить свечу</h2>

  <form method="post" class="admin-form">
    <input type="hidden" name="action" value="add">

    <label>
      Название
      <input name="name" required>
    </label>

    <label>
      Описание
      <textarea name="description" rows="4"></textarea>
    </label>

    <div class="form-row">
      <label>
        Цена (₽)
        <input type="number" name="price" step="0.01" required>
      </label>

      <label>
        Количество
        <input type="number" name="quantity" value="1" min="0">
      </label>
    </div>

    <button class="btn accent" type="submit">
      Добавить товар
    </button>
  </form>

  <h2 class="section-title">Мои товары</h2>

  <?php if (empty($products)): ?>
    <p>Вы ещё не добавили товары.</p>
  <?php else: ?>

  <div class="products-table-wrapper">
    <table class="products-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Название</th>
          <th>Цена</th>
          <th>Кол-во</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td>#<?= intval($p['id']) ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= number_format($p['price'], 2, '.', ',') ?> ₽</td>
            <td><?= intval($p['quantity']) ?></td>
            <td>
              <a class="btn danger small"
                 href="?delete=<?= intval($p['id']) ?>"
                 onclick="return confirm('Удалить товар?')">
                 Удалить
              </a>
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

</body>
</html>
