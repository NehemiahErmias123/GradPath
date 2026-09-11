<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/jwt.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Email and password are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password."]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email, password_hash, failed_attempts, locked_until FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password."]);
    exit;
}

// Check if account is currently locked
if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
    $minutes_left = ceil((strtotime($user['locked_until']) - time()) / 60);
    http_response_code(429);
    echo json_encode(["error" => "Too many failed attempts. Try again in $minutes_left minute(s)."]);
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    $new_attempts = $user['failed_attempts'] + 1;

    if ($new_attempts >= 5) {
        // Lock account for 15 minutes
        $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
        $stmt->execute([$user['id']]);
        http_response_code(429);
        echo json_encode(["error" => "Too many failed attempts. Account locked for 15 minutes."]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
    $stmt->execute([$new_attempts, $user['id']]);

    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password."]);
    exit;
}

// Successful login - reset failed attempts
$stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

$token = generate_jwt($user['id'], $user['email']);

http_response_code(200);
echo json_encode([
    "message" => "Login successful.",
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email']
    ]
]);