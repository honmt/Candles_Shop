<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
$ADMIN_CODE = 'ADMIN2026'; // секретный код администратора

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $admin_code = $_POST['admin_code'] ?? '';

    if ($username === '') $errors[] = 'Введите логин';
    if ($password === '') $errors[] = 'Введите пароль';

    if ($role === 'admin' && $admin_code !== $ADMIN_CODE) {
        $errors[] = 'Неверный код администратора';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('ssss', $username, $email, $hash, $role);

        if ($stmt->execute()) {
            $_SESSION['user'] = [
                'id' => $stmt->insert_id,
                'username' => $username,
                'role' => $role
            ];
            header('Location: ../index.php');
            exit;
        } else {
            $errors[] = 'Ошибка при регистрации: ' . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="form-box">

  <!-- КНОПКИ СВЕРХУ -->
  <div class="form-tabs">
    <a href="../index.php" class="tab-btn">Главная</a>
    <a href="login.php" class="tab-btn">Вход</a>
    <a href="register.php" class="tab-btn active">Регистрация</a>
  </div>

  <h2>Регистрация</h2>

  <?php if ($errors): foreach ($errors as $e): ?>
    <p class="error"><?= htmlspecialchars($e) ?></p>
  <?php endforeach; endif; ?>

  <form method="post">

    <label>
      Логин
      <input name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </label>

    <label>
      Email
      <input name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label>

    <label>
      Пароль
      <input type="password" name="password">
    </label>

    <label>
      Роль
      <select name="role" id="role" onchange="toggleAdminCode()">
        <option value="user">Покупатель</option>
        <option value="seller">Продавец</option>
        <option value="admin">Администратор</option>
      </select>
    </label>

    <div id="adminCode" style="display:none;">
      <label>
        Код администратора
        <input name="admin_code">
      </label>
    </div>

    <button class="btn accent" type="submit">Зарегистрироваться</button>
  </form>

</div>

<script>
function toggleAdminCode() {
    const role = document.getElementById('role').value;
    document.getElementById('adminCode').style.display =
        role === 'admin' ? 'block' : 'none';
}
</script>

</body>
</html>
