<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

header("Content-Type: application/json");

$user = verifyJWT();

$stmt = $pdo->prepare("
  SELECT id, name, email, phone, role, created_at
  FROM users
  WHERE id = :id
");
$stmt->execute([":id" => $user['id']]);

$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    http_response_code(404);
    echo json_encode(["success" => false]);
    exit;
}

echo json_encode([
  "success" => true,
  "user" => $data
]);
