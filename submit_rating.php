<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/jwt_utils.php';

$user = verifyJWT();
$data = json_decode(file_get_contents("php://input"), true);

$rating = (int)$data['rating'];
$message = trim($data['message']);

try {
    $stmt = $pdo->prepare("
      INSERT INTO feedback (user_id, rating, message)
      VALUES (:uid, :r, :m)
    ");
    $stmt->execute([
        ":uid" => $user['id'],
        ":r" => $rating,
        ":m" => $message
    ]);
    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "Already rated"]);
}
