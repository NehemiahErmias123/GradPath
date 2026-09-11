<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

$stmt = $pdo->prepare("
    SELECT utp.id, utp.status, utp.deadline, utp.notes, utp.created_at,
           p.id AS program_id, p.university_name, p.program_name, p.country, p.degree_type,
           (SELECT COUNT(*) FROM checklist_items WHERE tracked_program_id = utp.id) AS total_items,
           (SELECT COUNT(*) FROM checklist_items WHERE tracked_program_id = utp.id AND is_completed = 1) AS completed_items
    FROM user_tracked_programs utp
    JOIN programs p ON utp.program_id = p.id
    WHERE utp.user_id = ?
    ORDER BY utp.created_at DESC
");
$stmt->execute([$user->user_id]);
$tracked = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($tracked);