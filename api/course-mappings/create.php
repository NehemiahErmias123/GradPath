<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$course_id = $data['course_id'] ?? null;
$program_id = $data['program_id'] ?? null;
$prerequisite_name = $data['prerequisite_name'] ?? null;

if (!$course_id || !$program_id || !$prerequisite_name) {
    http_response_code(400);
    echo json_encode(["error" => "course_id, program_id, and prerequisite_name are required."]);
    exit;
}

// Verify the course belongs to this user
$check = $pdo->prepare("SELECT id FROM courses WHERE id = ? AND user_id = ?");
$check->execute([$course_id, $user->user_id]);
if (!$check->fetch()) {
    http_response_code(403);
    echo json_encode(["error" => "You don't have access to this course."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO course_mappings (course_id, program_id, prerequisite_name, status) VALUES (?, ?, ?, 'satisfied')");
$stmt->execute([$course_id, $program_id, $prerequisite_name]);

http_response_code(201);
echo json_encode(["message" => "Course mapped to prerequisite.", "id" => $pdo->lastInsertId()]);