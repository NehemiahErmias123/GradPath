<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$tracked_program_id = $_GET['tracked_program_id'] ?? null;

if (!$tracked_program_id) {
    http_response_code(400);
    echo json_encode(["error" => "tracked_program_id is required."]);
    exit;
}

// Verify this tracked program actually belongs to the logged-in user
$check = $pdo->prepare("SELECT id FROM user_tracked_programs WHERE id = ? AND user_id = ?");
$check->execute([$tracked_program_id, $user->user_id]);
if (!$check->fetch()) {
    http_response_code(403);
    echo json_encode(["error" => "You don't have access to this tracked program."]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM checklist_items WHERE tracked_program_id = ? ORDER BY id");
$stmt->execute([$tracked_program_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($items);