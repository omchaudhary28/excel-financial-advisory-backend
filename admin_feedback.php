<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/jwt_utils.php';

$user = verifyJWT();
if ($user['role'] !== 'admin') exit;

$stmt = $pdo->query("
  SELECT f.*, u.name, u.email, u.phone
  FROM feedback f
  JOIN users u ON u.id = f.user_id
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
