<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user']['id'];
$success = '';
$errors = [];

// Получаем текущие данные
$stmt = $mysqli->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Обновление данных
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($username === '') {
        $errors[] = 'Логин не может быть пустым';
    }

    if (empty($errors)) {
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare(
                "UPDATE users SET username=?, email=?, password=? WHERE id=?"
            );
            $stmt->bind_param('sssi', $username, $email, $hash, $user_id);
        } else {
            $stmt = $mysqli->prepare(
                "UPDATE users SET username=?, email=? WHERE id=?"
            );
            $stmt->bind_param('ssi', $username, $email, $user_id);
        }

        $stmt->execute();
        $stmt->close();

        $_SESSION['user']['username'] = $username;
        $success = 'Данные успешно обновлены';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Мой профиль</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<header class="site-header">
  <h1>Личный кабинет</h1>
  <nav>
    <a href="../index.php">Главная</a>
    <a href="../catalog.php">Каталог</a>
    <a href="index.php">Мой кабинет</a>
    <a href="profile.php" class="active">Профиль</a>
    <a href="../cart/view.php" class="nav-link accent">Корзина</a>
    <a href="../auth/logout.php" class="danger">
      Выйти (<?= htmlspecialchars($_SESSION['user']['username']) ?>)
    </a>
  </nav>
</header>

<main class="container">

  <h2 class="section-title">Редактирование профиля</h2>

  <div class="admin-form">

    <?php if ($success): ?>
      <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <?php foreach ($errors as $e): ?>
      <p class="error"><?= htmlspecialchars($e) ?></p>
    <?php endforeach; ?>

    <form method="post">

      <label>
        Логин
        <input name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
      </label>

      <label>
        Email
        <input name="email" value="<?= htmlspecialchars($user['email']) ?>">
      </label>

      <label>
        Новый пароль
        <input type="password" name="password" placeholder="Оставьте пустым, если не менять">
      </label>

      <button class="btn accent" type="submit">
        Сохранить изменения
      </button>

    </form>

  </div>

</main>

<footer class="site-footer">
  <p>© <?= date('Y') ?> Осенний магазин свечей</p>
</footer>

</body>
</html>
