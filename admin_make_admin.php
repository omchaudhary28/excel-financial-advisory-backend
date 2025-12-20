<?php

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/middleware_auth.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔐 Admin-only authentication
authenticate(true);

$data = json_decode(file_get_contents("php://input"), true);
$userId = $data['user_id'] ?? null;

if (!$userId) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "User ID required"
    ]);
    exit;
}

// Promote user to admin
$stmt = $pdo->prepare(
    "UPDATE users SET role = 'admin' WHERE id = :id"
);

$stmt->execute([
    ':id' => $userId
]);

echo json_encode([
    "success" => true,
    "message" => "User promoted to admin"
]);

exit;
