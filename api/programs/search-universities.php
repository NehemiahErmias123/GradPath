<?php
require_once __DIR__ . '/../config/cors.php';
header("Content-Type: application/json");

$name = $_GET['name'] ?? null;

if (!$name) {
    http_response_code(400);
    echo json_encode(["error" => "A 'name' query parameter is required."]);
    exit;
}

$url = "http://universities.hipolabs.com/search?name=" . urlencode($name);

$response = file_get_contents($url);

if ($response === false) {
    http_response_code(502);
    echo json_encode(["error" => "Failed to reach external university API."]);
    exit;
}

echo $response;