<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

require_auth();

$data = json_decode(file_get_contents("php://input"), true);

$university_name = $data['university_name'] ?? null;
$program_name = $data['program_name'] ?? null;
$country = $data['country'] ?? null;
$degree_type = $data['degree_type'] ?? null;

if (!$university_name || !$program_name) {
    http_response_code(400);
    echo json_encode(["error" => "University name and program name are required."]);
    exit;
}

if (strlen($university_name) > 150 || strlen($program_name) > 150) {
    http_response_code(400);
    echo json_encode(["error" => "University or program name is too long."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO programs (university_name, program_name, country, degree_type) VALUES (?, ?, ?, ?)");
$stmt->execute([$university_name, $program_name, $country, $degree_type]);

http_response_code(201);
echo json_encode(["message" => "Program created.", "id" => $pdo->lastInsertId()]);