<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$category_id = $data['category_id'] ?? null;
$title = $data['title'] ?? null;
$description = $data['description'] ?? null;

if (!$category_id || !$title) {
    http_response_code(400);
    echo json_encode(["error" => "category_id and title are required."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO checklist_templates (category_id, user_id, title, description) VALUES (?, ?, ?, ?)");
$stmt->execute([$category_id, $user->user_id, $title, $description]);

http_response_code(201);
echo json_encode(["message" => "Template created.", "id" => $pdo->lastInsertId()]);