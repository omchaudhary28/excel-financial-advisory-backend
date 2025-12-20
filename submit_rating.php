<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/middleware_auth.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json");

// 🔐 authenticated user
$user = authenticate(false);
$userId = $user['id'];

$data = json_decode(file_get_contents("php://input"), true);

$rating = (int)($data['rating'] ?? 0);
$message = trim($data['message'] ?? '');

if ($rating < 1 || $rating > 5 || $message === '') {
  http_response_code(400);
  echo json_encode(["success" => false]);
  exit;
}

$stmt = $pdo->prepare(
  "INSERT INTO feedback (user_id, rating, message)
  VALUES (:uid, :rating, :message)"
);

$stmt->execute([
  ':uid' => $userId,
  ':rating' => $rating,
  ':message' => $message
]);

echo json_encode(["success" => true]);
