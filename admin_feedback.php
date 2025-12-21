<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

// ---------- AUTH ----------
$headers = getallheaders();
$token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');

$user = verifyJWT($token);
if (!$user || ($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Admin access required"
    ]);
    exit;
}

// ---------- QUERY ----------
$stmt = $pdo->query("
  SELECT f.*, u.name, u.email, u.phone
  FROM feedback f
  JOIN users u ON u.id = f.user_id
");

// ---------- RESPONSE ----------
echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
