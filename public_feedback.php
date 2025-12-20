<?php
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Content-Type: application/json");

require_once __DIR__.'/db.php';

$stmt = $pdo->query("
  SELECT u.name, u.avatar, f.rating, f.message
  FROM feedback f
  JOIN users u ON u.id = f.user_id
  WHERE f.approved = true
");

echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
