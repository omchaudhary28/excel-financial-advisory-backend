<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

header("Content-Type: application/json");

$user = verifyJWT();

if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["success" => false]);
    exit;
}

$stmt = $pdo->query("
  SELECT
    f.id,
    f.rating,
    f.message,
    f.created_at,
    u.name,
    u.email,
    u.phone
  FROM feedback f
  JOIN users u ON f.user_id = u.id
  ORDER BY f.created_at DESC
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
