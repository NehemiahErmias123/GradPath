<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';

$stmt = $pdo->query("SELECT * FROM programs ORDER BY created_at DESC");
$programs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($programs);