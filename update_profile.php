<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php'; // 🔥 THIS WAS MISSING

$userData = verifyJWT();
$userId = $userData['id'];

$input = json_decode(file_get_contents("php://input"), true);

$name  = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');

if ($name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing fields"
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "UPDATE users SET name = :name, phone = :phone WHERE id = :id"
);

$stmt->execute([
    ":name"  => $name,
    ":phone" => $phone,
    ":id"    => $userId
]);

echo json_encode([
    "success" => true,
    "message" => "Profile updated successfully"
]);
