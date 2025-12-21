<?php

include_once 'jwt_utils.php';

function authenticate($adminOnly = false)
{
    header("Content-Type: application/json");

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
    $token = trim(str_replace('Bearer', '', $authHeader));

    if ($token === '') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid token"
        ]);
        exit;
    }

    // ✅ CORRECT CALL
    $user = verifyJWT($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized"
        ]);
        exit;
    }

    if ($adminOnly && ($user['role'] ?? 'user') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Admin access required"
        ]);
        exit;
    }

    return $user;
}
