<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$stmt = $pdo->prepare("SELECT * FROM requirement_categories WHERE user_id IS NULL OR user_id = ? ORDER BY name");
$stmt->execute([$user->user_id]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($categories);