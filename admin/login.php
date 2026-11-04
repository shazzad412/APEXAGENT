<?php
session_start();
require_once "../includes/db.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin["password"])) {
        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        $_SESSION["admin_logged_in"] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - APEX AGENT</title>
<style>
body{margin:0;font-family:Arial;background:#f3f4f6;display:flex;justify-content:center;align-items:center;min-height:100vh}
.login-box{width:400px;background:white;padding:35px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.1)}
h1{text-align:center}
input{width:100%;padding:12px;margin-bottom:18px;border:1px solid #ddd;border-radius:6px}
button{width:100%;padding:13px;background:#111827;color:white;border:none;border-radius:6px;cursor:pointer}
.error{background:#fee2e2;color:#b91c1c;padding:10px;border-radius:6px;margin-bottom:18px}
</style>
</head>
<body>
<div class="login-box">
<h1>APEX AGENT</h1>
<?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
<form method="POST">
<input type="text" name="username" placeholder="Username" value="admin" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
</div>
</body>
</html>
Se