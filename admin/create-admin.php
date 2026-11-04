<?php

require_once "../includes/db.php";

$username = "admin";
$email = "admin@apexagent.com";
$password = password_hash("123456", PASSWORD_DEFAULT);

try {

    $stmt = $pdo->prepare(
        "INSERT INTO admins (username, email, password)
         VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $username,
        $email,
        $password
    ]);

    echo "Admin created successfully.<br>";
    echo "Username: admin<br>";
    echo "Password: 123456<br>";
    echo "<br>Please delete create-admin.php after this.";

} catch (PDOException $e) {

    echo "Error: " . $e->getMessage();

}