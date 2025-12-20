<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/jwt_utils.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json; charset=UTF-8");

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Authenticate user via JWT
 * @param bool $adminOnly
 * @return array decoded token
 */
function authenticate(bool $adminOnly = false): array
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Authorization header missing"]);
        exit;
    }

    $token = str_replace("Bearer ", "", $headers['Authorization']);

    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        $user = (array) $decoded;

        if ($adminOnly && ($user['role'] ?? '') !== 'admin') {
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Admin access required"]);
            exit;
        }

        return $user;

    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Invalid or expired token"]);
        exit;
    }
}
