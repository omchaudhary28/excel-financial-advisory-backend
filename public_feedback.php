<?php
// ---------- CORS ----------
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Max-Age: 86400");
header("Content-Type: application/json");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ---------- DB ----------
require_once __DIR__ . '/db.php';

// ---------- QUERY ----------
$stmt = $pdo->query("
  SELECT 
    u.name,
    u.avatar,
    f.rating,
    f.message,
    f.created_at
  FROM feedback f
  JOIN users u ON u.id = f.user_id
  WHERE f.approved = 1
  ORDER BY f.created_at DESC
");

// ---------- RESPONSE ----------
echo json_encode([
  "success" => true,
  "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
