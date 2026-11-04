<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli("localhost", "root", "", "apex-agent");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    die("Invalid property ID.");
}

/*
 * আগে property-এর image filename বের করছি,
 * যাতে property delete করার পরে তার image-ও মুছে ফেলা যায়।
 */
$stmt = $conn->prepare("SELECT image FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    die("Property not found.");
}

$imageName = basename((string)($property['image'] ?? ''));

/*
 * Database থেকে property delete করছি
 */
$stmt = $conn->prepare("DELETE FROM properties WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Delete failed: " . $stmt->error);
}

/*
 * Property-এর image থাকলে uploads folder থেকেও মুছে দিচ্ছি
 */
if ($imageName !== '') {
    $imagePath = __DIR__ . "/../uploads/" . $imageName;

    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

$conn->close();

/*
 * Delete হওয়ার পরে manage properties page-এ ফেরত যাবে
 */
header("Location: manage_properties.php");
exit;
?>