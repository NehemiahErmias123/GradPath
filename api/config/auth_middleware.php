<?php
require_once __DIR__ . '/jwt.php';

function require_auth() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "No token provided."]);
        exit;
    }

    $token = $matches[1];
    $decoded = verify_jwt($token);

    if (!$decoded) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired token."]);
        exit;
    }

    return $decoded;
}