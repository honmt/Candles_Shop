<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];
if($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if($username=='' || $password=='') {
        $errors[]='Введите логин и пароль';
    }

    if(empty($errors)) {
        $stmt = $mysqli->prepare("SELECT id, username, password, role FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if($row = $res->fetch_assoc()) {
            if(password_verify($password, $row['password'])) {
                $_SESSION['user'] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'role' => $row['role']
                ];
                header('Location: ../index.php');
                exit;
            } else {
                $errors[]='Неверный логин или пароль';
            }
        } else {
            $errors[]='Неверный логин или пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Вход</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="form-box">

  <!-- КНОПКИ СВЕРХУ -->
  <div class="form-tabs">
    <a href="../index.php" class="tab-btn">Главная</a>
    <a href="login.php" class="tab-btn active">Вход</a>
    <a href="register.php" class="tab-btn">Регистрация</a>
  </div>

  <h2>Вход в аккаунт</h2>

  <?php if($errors): foreach($errors as $e): ?>
    <p class="error"><?=htmlspecialchars($e)?></p>
  <?php endforeach; endif; ?>

  <form method="post">
    <label>
      Логин
      <input name="username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
    </label>

    <label>
      Пароль
      <input type="password" name="password">
    </label>

    <button class="btn accent" type="submit">Войти</button>
  </form>

</div>

</body>
</html>
