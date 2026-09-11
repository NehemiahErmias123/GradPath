<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$tracked_program_id = $data['tracked_program_id'] ?? null;
$title = $data['title'] ?? null;

if (!$tracked_program_id || !$title) {
    http_response_code(400);
    echo json_encode(["error" => "tracked_program_id and title are required."]);
    exit;
}

// Verify ownership
$check = $pdo->prepare("SELECT id FROM user_tracked_programs WHERE id = ? AND user_id = ?");
$check->execute([$tracked_program_id, $user->user_id]);
if (!$check->fetch()) {
    http_response_code(403);
    echo json_encode(["error" => "You don't have access to this tracked program."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO checklist_items (tracked_program_id, template_id, title) VALUES (?, NULL, ?)");
$stmt->execute([$tracked_program_id, $title]);

http_response_code(201);
echo json_encode(["message" => "Item added.", "id" => $pdo->lastInsertId()]);