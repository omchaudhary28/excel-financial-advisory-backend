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

/**
 * --------------------------------------------------
 * SAFELY READ INPUT (JSON + FormData)
 * --------------------------------------------------
 */

// Try JSON body
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

// Name (support multiple keys)
$name = trim(
    $_POST['name']
    ?? $_POST['full_name']
    ?? $data['name']
    ?? $data['full_name']
    ?? ''
);

// Phone (support multiple keys)
$phone = trim(
    $_POST['phone']
    ?? $_POST['mobile']
    ?? $data['phone']
    ?? $data['mobile']
    ?? ''
);

// ✅ Validation
if ($name === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Name is required"
    ]);
    exit;
}

/**
 * --------------------------------------------------
 * UPDATE USER
 * --------------------------------------------------
 */
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
