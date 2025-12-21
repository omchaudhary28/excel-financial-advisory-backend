<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ---------- DB & AUTH ----------
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

// ---------- VERIFY ADMIN ----------
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

$user = verifyJWT($token);

if (!$user || ($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Admin access required"
    ]);
    exit;
}

// ---------- INPUT ----------
$data = json_decode(file_get_contents("php://input"), true);

$id = (int)($data['id'] ?? 0);
$approved = isset($data['approved']) ? (int)(bool)$data['approved'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid feedback ID"
    ]);
    exit;
}

// ---------- UPDATE ----------
$stmt = $pdo->prepare(
    "UPDATE feedback SET approved = :approved WHERE id = :id"
);

$stmt->execute([
    ":approved" => $approved,
    ":id" => $id
]);

// ---------- RESPONSE ----------
echo json_encode([
    "success" => true
]);
