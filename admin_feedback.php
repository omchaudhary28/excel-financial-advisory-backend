<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

header("Content-Type: application/json");

// 🔐 Verify JWT (admin only)
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

$stmt = $pdo->query("
  SELECT f.*, u.name, u.email, u.phone
  FROM feedback f
  JOIN users u ON u.id = f.user_id
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
