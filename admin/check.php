<?php
require_once "../includes/db.php";

echo "<h2>Check DB</h2>";

// 1. Table ase kina
$result = $pdo->query("SELECT * FROM admins");
$rows = $result->fetchAll(PDO::FETCH_ASSOC);

echo "Total admins: " . count($rows) . "<br><br>";

foreach($rows as $r){
    echo "ID: ".$r['id']." | Username: ".$r['username']." | Password hash: ".substr($r['password'],0,20)."..<br>";
    echo "Length: ".strlen($r['password'])."<br>";
    echo "Verify admin123: ".(password_verify("admin123", $r['password']) ? "YES - Thik ase" : "NO - Hash noshto")."<br><br>";
}

if(count($rows)==0){
    echo "Table khali! Admin dhuke nai.";
}
?>