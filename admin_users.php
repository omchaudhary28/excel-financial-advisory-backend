<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
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
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Admin access required"
    ]);
    exit;
}

// ---------- QUERY ----------
$stmt = $pdo->query("
    SELECT id, name, email, phone, role, created_at
    FROM users
    ORDER BY created_at DESC
");

// ---------- RESPONSE ----------
echo json_encode([
    "success" => true,
    "users" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
