<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$program_id = $data['program_id'] ?? null;

if (!$program_id) {
    http_response_code(400);
    echo json_encode(["error" => "program_id is required."]);
    exit;
}

$check = $pdo->prepare("SELECT id FROM user_tracked_programs WHERE user_id = ? AND program_id = ?");
$check->execute([$user->user_id, $program_id]);
if ($check->fetch()) {
    http_response_code(409);
    echo json_encode(["error" => "You are already tracking this program."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO user_tracked_programs (user_id, program_id, status) VALUES (?, ?, 'Researching')");
$stmt->execute([$user->user_id, $program_id]);
$tracked_program_id = $pdo->lastInsertId();

// Auto-generate checklist items from all applicable templates
$templates = $pdo->prepare("SELECT id, title FROM checklist_templates WHERE user_id IS NULL OR user_id = ?");
$templates->execute([$user->user_id]);
$all_templates = $templates->fetchAll(PDO::FETCH_ASSOC);

$insert_item = $pdo->prepare("INSERT INTO checklist_items (tracked_program_id, template_id, title) VALUES (?, ?, ?)");
foreach ($all_templates as $template) {
    $insert_item->execute([$tracked_program_id, $template['id'], $template['title']]);
}

http_response_code(201);
echo json_encode(["message" => "Program added to your tracker.", "id" => $tracked_program_id]);