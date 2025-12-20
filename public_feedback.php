<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $stmt = $pdo->query("
        SELECT 
            u.name,
            f.rating,
            f.message,
            f.created_at
        FROM feedback f
        INNER JOIN users u ON u.id = f.user_id
        ORDER BY f.created_at DESC
        LIMIT 12
    ");

    $feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $feedback
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to load feedback"
    ]);
}
