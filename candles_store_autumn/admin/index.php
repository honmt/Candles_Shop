<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="page-wrapper">

<header class="site-header">
  <h1>Админ-панель</h1>

  <nav>
    <a href="../index.php">Главная</a>
    <a href="../catalog.php">Каталог</a>
    <a href="index.php">Админка</a>
    <a href="orders.php">Заказы</a>
    <a href="users.php">Пользователи</a>
    <a href="products.php" class="active">Товары</a>
    <a href="../auth/logout.php">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>

<main class="container">
    <h1 class="page-title">Админ-панель</h1>
    <p class="page-subtitle">
        Управление магазином свечей: заказы, товары и пользователи
    </p>

    <!-- КАРТОЧКИ -->
    <section class="admin-actions">
        <div class="admin-card">
            <h3>📦 Заказы</h3>
            <p>Просмотр и управление заказами покупателей</p>
            <a href="orders.php" class="btn">Открыть</a>
        </div>

        <div class="admin-card">
            <h3>👤 Пользователи</h3>
            <p>Управление аккаунтами и ролями</p>
            <a href="users.php" class="btn">Открыть</a>
        </div>

        <div class="admin-card">
            <h3>🕯 Товары</h3>
            <p>Добавление, редактирование и удаление товаров</p>
            <a href="products.php" class="btn">Открыть</a>
        </div>
    </section>


    <section class="admin-section">
        <h2>Последние заказы</h2>

        <?php if (empty($orders)): ?>
            <p>Заказов пока нет.</p>
        <?php else: ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Товар</th>
                    <th>Кол-во</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= intval($o['order_id']) ?></td>
                    <td><?= htmlspecialchars($o['username']) ?></td>
                    <td><?= htmlspecialchars($o['product_name']) ?></td>
                    <td><?= intval($o['quantity']) ?></td>
                    <td>
                        <span class="status <?= htmlspecialchars($o['status']) ?>">
                            <?= htmlspecialchars($o['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="orders.php" class="table-btn edit">Открыть</a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

        <?php endif; ?>
    </section>

</main>

<footer class="footer">
    <p>© <?= date('Y') ?> Candles Store · Админ-панель</p>
</footer>

</body>
</html>
