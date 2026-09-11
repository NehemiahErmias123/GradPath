<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$data = json_decode(file_get_contents("php://input"), true);
$course_name = $data['course_name'] ?? null;
$institution = $data['institution'] ?? null;
$grade = $data['grade'] ?? null;
$credits = $data['credits'] ?? null;

if (!$course_name) {
    http_response_code(400);
    echo json_encode(["error" => "course_name is required."]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO courses (user_id, course_name, institution, grade, credits) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$user->user_id, $course_name, $institution, $grade, $credits]);

http_response_code(201);
echo json_encode(["message" => "Course added.", "id" => $pdo->lastInsertId()]);