<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$category_id = $_GET['category_id'] ?? null;

if (!$category_id) {
    http_response_code(400);
    echo json_encode(["error" => "category_id is required."]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT * FROM checklist_templates
    WHERE category_id = ? AND (user_id IS NULL OR user_id = ?)
    ORDER BY id
");
$stmt->execute([$category_id, $user->user_id]);
$templates = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($templates);