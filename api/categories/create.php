<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$name = $data['name'] ?? null;
$description = $data['description'] ?? null;

if (!$name) {
    http_response_code(400);
    echo json_encode(["error" => "Category name is required."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO requirement_categories (user_id, name, description) VALUES (?, ?, ?)");
$stmt->execute([$user->user_id, $name, $description]);

http_response_code(201);
echo json_encode(["message" => "Category created.", "id" => $pdo->lastInsertId()]);