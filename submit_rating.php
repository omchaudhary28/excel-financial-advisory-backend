<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: https://excel-financial-advisory.vercel.app");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/jwt_utils.php";

$user = verifyJWT();
$userId = $user["id"];

$data = json_decode(file_get_contents("php://input"), true);

$rating = (int)($data["rating"] ?? 0);
$message = trim($data["message"] ?? "");

if ($rating < 1 || $rating > 5 || $message === "") {
    http_response_code(400);
    echo json_encode(["success" => false]);
    exit;
}

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
