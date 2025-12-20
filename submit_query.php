<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/jwt_utils.php';

header("Content-Type: application/json");

$user = verifyJWT();
$userId = $user['id'];

$data = json_decode(file_get_contents("php://input"), true);

$rating = (int)($data['rating'] ?? 0);
$message = trim($data['message'] ?? '');

if ($rating < 1 || $rating > 5 || $message === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid input"
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO feedback (user_id, rating, message)
         VALUES (:uid, :rating, :message)"
    );

    $stmt->execute([
        ":uid" => $userId,
        ":rating" => $rating,
        ":message" => $message
    ]);

    echo json_encode(["success" => true]);

} catch (PDOException $e) {
    // UNIQUE constraint: one feedback per user
    if ($e->getCode() === "23505") {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "message" => "You have already submitted feedback."
        ]);
        exit;
    }

    throw $e;
}
