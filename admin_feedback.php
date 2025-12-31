<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php';

authenticate(true);

$stmt = $pdo->query("
  SELECT 
    f.id,
    f.rating,
    f.message,
    f.approved,
    f.created_at,
    u.name,
    u.email
  FROM feedback f
  JOIN users u ON u.id = f.user_id
  ORDER BY f.created_at DESC
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
