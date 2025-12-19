<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function verifyJWT(): array
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Authorization header missing"
        ]);
        exit;
    }

    $authHeader = $headers['Authorization'];

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid authorization format"
        ]);
        exit;
    }

    $token = trim(str_replace('Bearer', '', $authHeader));
    $secret = $_ENV['JWT_SECRET'];

    try {
        $decoded = JWT::decode($token, new Key($secret, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid or expired token"
        ]);
        exit;
    }
}
