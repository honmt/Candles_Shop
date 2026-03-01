<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header('Location: ../catalog.php');
    exit;
}

$method = $_POST['method'] ?? null;
$user_id = $_SESSION['user']['id'];

if (!$method) {
    header('Location: payment.php');
    exit;
}

// создаём заказ
$total = 0;
foreach ($_SESSION['cart'] as $qty) {
    $total += $qty * 100; // имитация, если цена не нужна
}

$mysqli->query("
    INSERT INTO orders (user_id, total, status, payment_method, is_paid)
    VALUES ($user_id, $total, 'paid', '$method', 1)
");

$order_id = $mysqli->insert_id;

// очищаем корзину
unset($_SESSION['cart']);

header("Location: order_success.php?order_id=$order_id");
exit;
