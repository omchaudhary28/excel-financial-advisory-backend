<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/middleware_auth.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json");

authenticate(true);

$stmt = $pdo->query("
  SELECT f.id, f.rating, f.message, f.created_at,
         u.name, u.email, u.phone
  FROM feedback f
  JOIN users u ON f.user_id = u.id
  ORDER BY f.created_at DESC
");

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["success" => true, "data" => $data]);
