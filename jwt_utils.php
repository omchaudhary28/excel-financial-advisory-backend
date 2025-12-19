<?php

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$JWT_SECRET = getenv('JWT_SECRET');

if (!$JWT_SECRET) {
    throw new Exception("JWT_SECRET not set");
}

function generateJWT(array $payload): string {
    global $JWT_SECRET;

    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24); // 24 hours

    return JWT::encode($payload, $JWT_SECRET, 'HS256');
}

function verifyJWT(): array {
    global $JWT_SECRET;

    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Missing token"]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    try {
        $decoded = JWT::decode($token, new Key($JWT_SECRET, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid token"]);
        exit;
    }
}
