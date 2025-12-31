<?php
require_once 'cors.php';
require_once 'db_connect.php';
require_once 'middleware_auth.php';

authenticate(true);

$stmt = $pdo->query("
  SELECT id, name, email
  FROM users
  ORDER BY created_at DESC
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
