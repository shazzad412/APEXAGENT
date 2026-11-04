<?php
require_once __DIR__ . "/includes/db.php";

$locations = [
    "Tokyo",
    "Fukushima",
    "Niigata"
];

foreach ($locations as $location) {

    // এই location-এ আগে কয়টি room আছে তা check করবে
    $countStmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM properties
        WHERE location = ?
        AND property_type = 'room'
    ");

    $countStmt->bind_param("s", $location);
    $countStmt->execute();

    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();

    $existingRooms = (int) $countRow["total"];

    // বাকি room তৈরি করবে, সর্বোচ্চ 100টি
    for ($i = $existingRooms + 1; $i <= 100; $i++) {

        $title = $location . " Rental Room " . $i;
        $image = "";
        $description = "Comfortable rental room in " . $location . ".";

        $insertStmt = $conn->prepare("
            INSERT INTO properties
            (title, location, property_type, image, description)
            VALUES (?, ?, 'room', ?, ?)
        ");

        $insertStmt->bind_param(
            "ssss",
            $title,
            $location,
            $image,
            
            $description
        );

        $insertStmt->execute();
    }
}

echo "<h2>Rooms created successfully.</h2>";
echo "<p>Tokyo: 100 rooms</p>";
echo "<p>Fukushima: 100 rooms</p>";
echo "<p>Niigata: 100 rooms</p>";
echo "<p>You can now delete seed.php.</p>";