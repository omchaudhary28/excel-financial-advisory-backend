<?php
require_once 'cors.php';
require_once 'jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ðŸ” Extract Bearer token
$token = JWT::getBearerToken();

if (!$token) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Authentication required: JWT token missing."
    ]);
    exit;
}

// ðŸ” Verify token
$payload = JWT::verify($token);

if (!$payload) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid or expired JWT token."
    ]);
    exit;
}

// âœ… Authenticated user is now available globally
$GLOBALS['authenticated_user'] = [
    'id'    => $payload['id'],
    'email' => $payload['email'],
    'role'  => $payload['role']
];

// Continue execution of protected route
