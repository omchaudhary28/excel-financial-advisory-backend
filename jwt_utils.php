<?php

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generateJWT(array $payload): string
{
    $secret = getenv("JWT_SECRET");

    if (!$secret) {
        throw new Exception("JWT_SECRET not set");
    }

    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24); // 24 hours

    return JWT::encode($payload, $secret, 'HS256');
}

function verifyJWT(): array
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Missing token"]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    try {
        return (array) JWT::decode(
            $token,
            new Key(getenv("JWT_SECRET"), 'HS256')
        );
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid token"]);
        exit;
    }
}
