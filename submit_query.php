<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db_connect.php';

header("Content-Type: application/json; charset=UTF-8");

// Accept JSON body
$data = json_decode(file_get_contents("php://input"), true);

$name    = trim($data['name'] ?? '');
$email   = trim($data['email'] ?? '');
$subject = trim($data['subject'] ?? '');
$message = trim($data['message'] ?? '');

// If you allow anonymous queries, keep this optional
$userId = $GLOBALS['authenticated_user']['user_id'] ?? null;

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Name, email and message are required"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare(
        "INSERT INTO queries (user_id, name, email, subject, message)
         VALUES (:user_id, :name, :email, :subject, :message)"
    );

    $stmt->execute([
        ':user_id' => $userId,
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject ?: null,
        ':message' => $message
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Query submitted successfully"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Failed to submit query"
    ]);
}
