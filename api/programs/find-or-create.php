<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$university_name = $data['university_name'] ?? null;
$country = $data['country'] ?? null;

if (!$university_name) {
    http_response_code(400);
    echo json_encode(["error" => "university_name is required."]);
    exit;
}

// Check if this university already exists in our programs table
$check = $pdo->prepare("SELECT id FROM programs WHERE university_name = ?");
$check->execute([$university_name]);
$existing = $check->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    echo json_encode(["program_id" => $existing['id']]);
    exit;
}

// Doesn't exist yet, create it with an honest placeholder
$stmt = $pdo->prepare("INSERT INTO programs (university_name, program_name, country, degree_type) VALUES (?, 'Graduate Programs', ?, 'Varies')");
$stmt->execute([$university_name, $country]);

echo json_encode(["program_id" => $pdo->lastInsertId()]);