<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

/*
  Public-safe feedback:
  - NO email
  - NO phone
  - NO user_id
*/
$stmt = $pdo->query("
    SELECT 
        u.name,
        f.rating,
        f.message,
        f.created_at
    FROM feedback f
    JOIN users u ON u.id = f.user_id
    ORDER BY f.created_at DESC
    LIMIT 10
");

$feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "data" => $feedback
]);
