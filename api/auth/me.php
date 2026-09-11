<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/auth_middleware.php';

$user = require_auth();

echo json_encode([
    "message" => "You are authenticated.",
    "user_id" => $user->user_id,
    "email" => $user->email
]);