<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$tracked_program_id = $data['tracked_program_id'] ?? null;

if (!$tracked_program_id) {
    http_response_code(400);
    echo json_encode(["error" => "tracked_program_id is required."]);
    exit;
}

// Verify ownership before deleting anything
$check = $pdo->prepare("SELECT id FROM user_tracked_programs WHERE id = ? AND user_id = ?");
$check->execute([$tracked_program_id, $user->user_id]);
if (!$check->fetch()) {
    http_response_code(403);
    echo json_encode(["error" => "You don't have access to this tracked program."]);
    exit;
}

// Delete checklist items first (foreign key dependency), then the tracked program itself
$pdo->prepare("DELETE FROM checklist_items WHERE tracked_program_id = ?")->execute([$tracked_program_id]);
$pdo->prepare("DELETE FROM user_tracked_programs WHERE id = ?")->execute([$tracked_program_id]);

echo json_encode(["message" => "Program removed from your tracker."]);