<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=UTF-8');

$data = json_decode(file_get_contents('php://input'), true);

$name    = trim($data['name'] ?? '');
$email   = trim($data['email'] ?? '');
$message = trim($data['message'] ?? '');
$subject = trim($data['subject'] ?? 'Contact Form');

if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'errors'  => ['All fields are required']
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO queries (name, email, subject, message)
         VALUES (:name, :email, :subject, :message)"
    );

    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'errors'  => ['Server error']
    ]);
}
