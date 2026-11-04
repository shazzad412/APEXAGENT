<?php
session_start();
require_once "../includes/db.php";

$username = "admin";
$password = "admin123";
$hash = password_hash($password, PASSWORD_DEFAULT);

$pdo->exec("DELETE FROM admins WHERE username='admin'");
$stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->execute([$username, $hash]);

echo "Admin banano done!<br>Username: admin<br>Password: admin123<br>";
echo "<a href='login.php'>Login e jao</a>";
?>