<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Удаление пользователя
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id !== $_SESSION['user']['id']) {
        $mysqli->query("DELETE FROM users WHERE id=$id");
    }
    header('Location: users.php');
    exit;
}

// Получаем пользователей
$users = [];
$res = $mysqli->query("SELECT id, username, email, role FROM users ORDER BY id ASC");
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Пользователи — Админка</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header class="site-header">
  <h1>Админ-панель</h1>
  <nav>
    <a href="../index.php">Главная</a>
    <a href="../catalog.php">Каталог</a>
    <a href="index.php">Админка</a>
    <a href="products.php" class="active">Товары</a>
    <a href="orders.php">Заказы</a>
    <a href="../auth/logout.php">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>

<main class="container">

  <h2 class="section-title">Пользователи системы</h2>

  <?php if (empty($users)): ?>
    <p>Пользователи не найдены.</p>
  <?php else: ?>

  <div class="users-table-wrapper">
    <table class="users-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Логин</th>
          <th>Email</th>
          <th>Роль</th>
          <th>Действия</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td>#<?= intval($u['id']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
              <span class="role-badge <?= htmlspecialchars($u['role']) ?>">
                <?= htmlspecialchars($u['role']) ?>
              </span>
            </td>
            <td>
              <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                <a class="btn danger small"
                   href="users.php?delete=<?= intval($u['id']) ?>"
                   onclick="return confirm('Удалить пользователя?')">
                   Удалить
                </a>
              <?php else: ?>
                <span class="muted">Это вы</span>
              <?php endif; ?>
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
