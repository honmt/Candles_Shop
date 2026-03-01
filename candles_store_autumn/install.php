<?php
// Simple installer: creates database and tables and a default admin user
$host='localhost'; $user='root'; $pass=''; $dbname='candles_store';

$mysqli = new mysqli($host, $user, $pass);
if ($mysqli->connect_errno) {
    die("Ошибка подключения к MySQL: " . $mysqli->connect_error);
}

$queries = [];

// Create database
$queries[] = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
$queries[] = "USE `{$dbname}`;";

// users table
$queries[] = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(200),
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// products table
$queries[] = "CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  seller_id INT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  quantity INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// orders table
$queries[] = "CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'processing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// order_items table
$queries[] = "CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

foreach($queries as $q) {
    if(!$mysqli->query($q)) {
        echo "<p>Ошибка выполнения запроса: " . $mysqli->error . "</p>";
    }
}

// Create default admin user if not exists
$adminLogin = 'admin';
$adminEmail = 'admin@example.com';
$adminPassPlain = 'admin'; // you can change after install
$hash = password_hash($adminPassPlain, PASSWORD_DEFAULT);

$res = $mysqli->query("SELECT id FROM users WHERE username='admin' LIMIT 1");
if($res && $res->num_rows==0) {
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
    $stmt->bind_param('sss', $adminLogin, $adminEmail, $hash);
    $stmt->execute();
    $stmt->close();
    echo "<p>Создан пользователь admin / пароль: admin</p>";
} else {
    echo "<p>Пользователь admin уже существует.</p>";
}

// Insert sample products if none exist
$res = $mysqli->query("SELECT id FROM products LIMIT 1");
if($res && $res->num_rows==0) {
    $mysqli->query(\"INSERT INTO products (seller_id, name, description, price, quantity) VALUES
    (NULL, 'Ароматическая свеча «Корица»', 'Теплая коричная нота, 40 часов горения', 450.00, 10),
    (NULL, 'Соeвая свеча «Тыква»', 'Натуральный соевый воск, мягкий аромат', 650.00, 8),
    (NULL, 'Декоративная свеча «Листопад»', 'Красивый дизайн для подарка', 520.00, 12)
    \");\n    echo \"<p>Добавлены примерные товары.</p>\";\n} else {\n    echo \"<p>Товары уже есть.</p>\";\n}\n\necho \"<p>Установка завершена. Теперь можно перейти на <a href='index.php'>главную</a>.</p>\";\n?>