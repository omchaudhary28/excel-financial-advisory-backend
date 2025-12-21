<?php
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

$user = verifyJWT();
if (($user['role'] ?? '') !== 'admin') {
    http_response_code(403);
    exit;
}

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
