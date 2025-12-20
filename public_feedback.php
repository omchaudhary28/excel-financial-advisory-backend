<?php
require_once __DIR__ . '/db.php';
header("Content-Type: application/json");

$stmt = $pdo->query("
  SELECT u.name, f.rating, f.message
  FROM feedback f
  JOIN users u ON f.user_id = u.id
  ORDER BY f.created_at DESC
  LIMIT 6
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
