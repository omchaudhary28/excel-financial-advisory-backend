<?php
require_once 'db.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

$name    = $data['name'] ?? '';
$email   = $data['email'] ?? '';
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO queries (name, email, subject, message)
     VALUES (:name, :email, :subject, :message)"
);

$stmt->execute([
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message
]);

echo json_encode([
    "success" => true,
    "message" => "Query submitted"
]);
