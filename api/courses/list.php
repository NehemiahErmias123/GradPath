<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$stmt = $pdo->prepare("SELECT * FROM courses WHERE user_id = ? ORDER BY id");
$stmt->execute([$user->user_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($courses);