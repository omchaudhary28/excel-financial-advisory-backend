<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Authenticate user via JWT
 * @param bool $adminOnly
 * @return array
 */
function authenticate(bool $adminOnly = false): array
{
    // ✅ verifyJWT() comes from jwt_utils.php
    $payload = verifyJWT();

    if (!isset($payload['id'], $payload['email'], $payload['role'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid authentication payload"
        ]);
        exit;
    }

    if ($adminOnly && $payload['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Forbidden: Admin access required"
        ]);
        exit;
    }

    return [
        'id'    => $payload['id'],
        'email' => $payload['email'],
        'role'  => $payload['role'],
    ];
}
