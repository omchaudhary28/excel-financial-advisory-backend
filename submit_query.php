<?php
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    !$data ||
    empty($data["name"]) ||
    empty($data["email"]) ||
    empty($data["message"])
) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON or missing fields"
    ]);
    exit;
}

$name = trim($data["name"]);
$email = trim($data["email"]);
$subject = $data["subject"] ?? null;
$message = trim($data["message"]);

$stmt = $pdo->prepare(
    "INSERT INTO queries (name, email, subject, message)
     VALUES (:name, :email, :subject, :message)"
);

$stmt->execute([
    "name" => $name,
    "email" => $email,
    "subject" => $subject,
    "message" => $message
]);

echo json_encode([
    "success" => true,
    "message" => "Query submitted"
]);
