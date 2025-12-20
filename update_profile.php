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

// 🔐 Authenticated user (NOT admin-only)
$user = authenticate(false);
$userId = $user['id'];

$data = json_decode(file_get_contents("php://input"), true);

$name  = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');

if ($name === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Name is required"
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE users
     SET name = :name, phone = :phone
     WHERE id = :id"
);

$stmt->execute([
    ':name'  => $name,
    ':phone' => $phone,
    ':id'    => $userId
]);

echo json_encode([
    "success" => true,
    "message" => "Profile updated successfully"
]);
exit;
