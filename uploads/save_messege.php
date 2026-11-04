<?php
$conn = new mysqli("localhost", "root", "", "apex-agent");

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];

$sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
$conn->query($sql);

echo "<h2>Message Sent Successfully!</h2>";
echo "<a href='contact.php'>Go Back</a>";
?>
