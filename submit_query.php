<?php
require_once __DIR__ . "/cors.php";
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON"]);
    exit;
}

$name    = trim($data['name'] ?? '');
$email   = trim($data['email'] ?? '');
$subject = trim($data['subject'] ?? '');
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$message) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO queries (name, email, subject, message)
     VALUES (:name, :email, :subject, :message)"
);

$stmt->execute([
    ":name" => $name,
    ":email" => $email,
    ":subject" => $subject,
    ":message" => $message
]);

echo json_encode(["success" => true, "message" => "Query submitted"]);
