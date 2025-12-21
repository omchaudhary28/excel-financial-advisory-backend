<?php
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/db.php';

$stmt = $pdo->query("
    SELECT 
        u.name,
        u.avatar,
        f.rating,
        f.message,
        f.created_at
    FROM feedback f
    JOIN users u ON u.id = f.user_id
    WHERE f.approved = TRUE
    ORDER BY f.created_at DESC
");

echo json_encode([
    "success" => true,
    "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
]);
