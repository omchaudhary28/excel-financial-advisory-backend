<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/middleware_auth.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 🔐 Authenticated user
$user = authenticate(false);
$userId = $user['id'];

$data = json_decode(file_get_contents("php://input"), true);

$rating  = (int)($data['rating'] ?? 0);
$message = trim($data['message'] ?? '');

if ($rating < 1 || $rating > 5 || $message === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid rating data"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO feedback (user_id, rating, message)
         VALUES (:uid, :rating, :message)"
    );

    $stmt->execute([
        ':uid'     => $userId,
        ':rating'  => $rating,
        ':message' => $message
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Thank you for your feedback!"
    ]);
    exit;

} catch (PDOException $e) {
    // 🔒 Unique constraint violation (already rated)
    if ($e->getCode() === '23505') {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "You have already submitted feedback."
        ]);
        exit;
    }

    // ❌ Any other DB error
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error. Please try again later."
    ]);
    exit;
}
