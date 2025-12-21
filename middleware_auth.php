<?php
require_once __DIR__ . '/jwt_utils.php';

/**
 * Authenticate user via JWT
 * @param bool $adminOnly
 * @return array
 */
function authenticate(bool $adminOnly = false): array
{
    $user = verifyJWT(); // 🔑 header-based, no arguments

    if ($adminOnly && ($user['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Admin access required"
        ]);
        exit;
    }

    return $user;
}
