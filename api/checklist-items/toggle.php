<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$item_id = $data['item_id'] ?? null;

if (!$item_id) {
    http_response_code(400);
    echo json_encode(["error" => "item_id is required."]);
    exit;
}

// Verify ownership through the join to user_tracked_programs
$check = $pdo->prepare("
    SELECT ci.id, ci.is_completed
    FROM checklist_items ci
    JOIN user_tracked_programs utp ON ci.tracked_program_id = utp.id
    WHERE ci.id = ? AND utp.user_id = ?
");
$check->execute([$item_id, $user->user_id]);
$item = $check->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    http_response_code(403);
    echo json_encode(["error" => "You don't have access to this item."]);
    exit;
}

$new_status = $item['is_completed'] ? 0 : 1;
$update = $pdo->prepare("UPDATE checklist_items SET is_completed = ? WHERE id = ?");
$update->execute([$new_status, $item_id]);

echo json_encode(["message" => "Item updated.", "is_completed" => $new_status]);