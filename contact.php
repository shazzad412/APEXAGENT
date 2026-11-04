<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set('Asia/Tokyo');

$conn = new mysqli("localhost", "root", "", "apex-agent");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $msg = trim($_POST['message'] ?? '');

    if ($name === "" || $email === "" || $msg === "") {
        $message = "Please fill all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $msg);

        if ($stmt->execute()) {
            $message = "Thank you! Your message has been saved successfully.";
            $messageType = "success";
            $_POST = [];
        } else {
            $message = "Failed to save message: " . $stmt->error;
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
            margin: 0;
        }
        .topbar {
            background: linear-gradient(90deg, #0d87ff, #0d6ef0);
            color: white;
            padding: 18px 28px;
            font-size: 42px;
            font-weight: 700;
        }
        .page-wrap {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
        }
        .title {
            font-size: 62px;
            font-weight: 300;
            margin-bottom: 15px;
        }
        .subtitle {
            font-size: 28px;
            margin-bottom: 24px;
            color: #444;
        }
        .field {
            width: 100%;
            height: 56px;
            font-size: 24px;
            padding: 0 18px;
            border: 2px solid #b7b7b7;
            border-radius: 8px;
            background: #f4f4f4;
            margin-bottom: 18px;
            box-sizing: border-box;
        }
        textarea.field {
            height: 140px;
            padding: 18px;
            resize: vertical;
        }
        .btn {
            width: 180px;
            height: 54px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(180deg, #1f9ef7, #0a7ae1);
            color: white;
            font-size: 26px;
            font-weight: 600;
            cursor: pointer;
        }
        .notice {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 18px;
        }
        .success {
            background: #d4edda;
            color: #0a6b1f;
            border: 1px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="topbar">
    Apex Agent株式会社
</div>

<div class="page-wrap">
    <h1 class="title">Contact Us / お問い合わせ</h1>
    <p class="subtitle">If you need help, please contact us.</p>

    <?php if ($message): ?>
        <div class="notice <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input class="field" type="text" name="name" placeholder="Your Name" required>
        <input class="field" type="email" name="email" placeholder="Your Email" required>
        <textarea class="field" name="message" placeholder="Your Message" required></textarea>
        <button type="submit" class="btn">Send</button>
    </form>
</div>

</body>
</html>