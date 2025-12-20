<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getJWTSecret() {
    $secret = getenv('JWT_SECRET');
    if (!$secret) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "JWT_SECRET not configured"
        ]);
        exit;
    }
    return $secret;
}

/* -------- GENERATE TOKEN (LOGIN) -------- */
function generateJWT(array $payload): string {
    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24 * 7); // 7 days

    return JWT::encode($payload, getJWTSecret(), 'HS256');
}

/* -------- VERIFY TOKEN (AUTH) -------- */
function verifyJWT(): array {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Authorization header missing"
        ]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    try {
        $decoded = JWT::decode($token, new Key(getJWTSecret(), 'HS256'));
        return (array)$decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid or expired token"
        ]);
        exit;
    }
}
