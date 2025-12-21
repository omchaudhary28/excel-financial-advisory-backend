<?php
require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * --------------------------------------------------
 * GET JWT SECRET
 * --------------------------------------------------
 */
function getJWTSecret(): string
{
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

/**
 * --------------------------------------------------
 * GENERATE JWT (LOGIN)
 * --------------------------------------------------
 */
function generateJWT(array $payload): string
{
    $payload['iat'] = time();
    $payload['exp'] = time() + (60 * 60 * 24 * 7); // 7 days

    return JWT::encode($payload, getJWTSecret(), 'HS256');
}

/**
 * --------------------------------------------------
 * VERIFY JWT (AUTH MIDDLEWARE)
 * --------------------------------------------------
 * Accepts token from middleware_auth.php
 */
function verifyJWT(string $token): array|false
{
    if (trim($token) === '') {
        return false;
    }

    try {
        $decoded = JWT::decode(
            $token,
            new Key(getJWTSecret(), 'HS256')
        );

        return [
            "id"    => $decoded->id ?? null,
            "email" => $decoded->email ?? null,
            "role"  => $decoded->role ?? 'user'
        ];
    } catch (Exception $e) {
        return false;
    }
}
