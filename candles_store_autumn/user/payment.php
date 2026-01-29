<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Оплата заказа</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="page">

<header class="site-header">
  <div class="header-inner">
    <div class="logo">🕯 Осенний магазин свечей</div>
  </div>
</header>

<main class="container">

<h2 class="section-title">Выберите способ оплаты</h2>

<form action="payment_process.php" method="post" class="payment-box">

  <label class="payment-option">
    <input type="radio" name="method" value="card" required>
    💳 Банковская карта
  </label>

  <label class="payment-option">
    <input type="radio" name="method" value="sbp">
    📱 СБП
  </label>

  <button class="btn accent">Оплатить</button>
</form>

</main>

<footer class="site-footer">
  <p>© <?=date('Y')?> Осенний магазин свечей</p>
</footer>

</div>

</body>
</html>
