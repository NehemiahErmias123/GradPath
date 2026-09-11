<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

define('JWT_SECRET', $_ENV['JWT_SECRET']);
define('JWT_ALGO', 'HS256');

function generate_jwt($user_id, $email) {
    $payload = [
        "iat" => time(),
        "exp" => time() + (60 * 60 * 24),
        "user_id" => $user_id,
        "email" => $email
    ];
    return JWT::encode($payload, JWT_SECRET, JWT_ALGO);
}

function verify_jwt($token) {
    try {
        return JWT::decode($token, new Key(JWT_SECRET, JWT_ALGO));
    } catch (Exception $e) {
        return null;
    }
}