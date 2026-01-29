<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'candle_store_autumn';

$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_errno) {
    // If DB doesn't exist, ask user to run installer
    echo "<p>Не удалось подключиться к базе данных ({$mysqli->connect_errno}): {$mysqli->connect_error}</p>";
    echo "<p>Если вы впервые запускаете проект — откройте <a href='/candles_store_autumn/install.php'>install.php</a> для создания БД.</p>";
    exit;
}
$mysqli->set_charset('utf8mb4');
