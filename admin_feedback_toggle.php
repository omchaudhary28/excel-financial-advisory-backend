<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Max-Age: 86400");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ---------- AUTH ----------
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

$user = verifyJWT();
if (($user['role'] ?? '') !== 'admin') {
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
$approved = (bool)($data['approved'] ?? false);

// ---------- UPDATE ----------
$stmt = $pdo->prepare(
    "UPDATE feedback SET approved = :approved WHERE id = :id"
);

$stmt->execute([
    ':approved' => $approved, // PostgreSQL BOOLEAN
    ':id' => $id
]);

echo json_encode(["success" => true]);
