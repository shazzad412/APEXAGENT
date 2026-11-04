<?php

require_once __DIR__ . "/includes/db.php";

$locations = [
    "Tokyo",
    "Fukushima",
    "Niigata"
];

try {
    foreach ($locations as $location) {

        // এই location-এ আগে কতগুলো room আছে তা গণনা করবে
        $countStmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM properties
            WHERE location = :location
            AND property_type = 'room'
        ");

        $countStmt->execute([
            ":location" => $location
        ]);

        $existingRooms = (int) $countStmt->fetchColumn();

        // সর্বোচ্চ 100টি room তৈরি করবে
        for ($i = $existingRooms + 1; $i <= 100; $i++) {

            $title = $location . " Rental Room " . $i;
            $image = "";
            $description = "Comfortable rental room in " . $location . ".";

            $insertStmt = $pdo->prepare("
                INSERT INTO properties
                (title, location, property_type, image, description)
                VALUES
                (:title, :location, 'room', :image, :description)
            ");

            $insertStmt->execute([
                ":title" => $title,
                ":location" => $location,
                ":image" => $image,
                ":description" => $description
            ]);
        }
    }

    echo "<h2>Rooms created successfully.</h2>";
    echo "<p>Tokyo: 100 rooms</p>";
    echo "<p>Fukushima: 100 rooms</p>";
    echo "<p>Niigata: 100 rooms</p>";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}