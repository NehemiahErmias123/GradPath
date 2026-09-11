<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$program_id = $_GET['program_id'] ?? null;

if (!$program_id) {
    http_response_code(400);
    echo json_encode(["error" => "program_id is required."]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT cm.id, cm.prerequisite_name, cm.status, c.course_name, c.institution, c.grade
    FROM course_mappings cm
    JOIN courses c ON cm.course_id = c.id
    WHERE cm.program_id = ? AND c.user_id = ?
    ORDER BY cm.id
");
$stmt->execute([$program_id, $user->user_id]);
$mappings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($mappings);