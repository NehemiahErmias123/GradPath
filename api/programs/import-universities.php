<?php
require_once __DIR__ . '/../config/db.php';

// Pull the entire global university list, no country filter
$url = "http://universities.hipolabs.com/search";
$response = file_get_contents($url);

if ($response === false) {
    die("Failed to reach Hipolabs API.");
}

$universities = json_decode($response, true);

// Shuffle so we get variety across countries, not just alphabetically first
shuffle($universities);

// Cap how many we actually insert
$limit = 150;
$universities = array_slice($universities, 0, $limit);

$total_inserted = 0;

foreach ($universities as $uni) {
    $name = $uni['name'];
    $uni_country = $uni['country'];

    $check = $pdo->prepare("SELECT id FROM programs WHERE university_name = ?");
    $check->execute([$name]);
    if ($check->fetch()) {
        continue;
    }

    $stmt = $pdo->prepare("INSERT INTO programs (university_name, program_name, country, degree_type) VALUES (?, 'Graduate Programs', ?, 'Varies')");
    $stmt->execute([$name, $uni_country]);
    $total_inserted++;
}

echo "Done. Inserted $total_inserted new universities out of $limit checked.";