<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

foreach (['name', 'email', 'message'] as $field) {
    if (empty($data[$field])) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'error' => "$field is required"
        ]);
        exit;
    }
}

$stmt = $pdo->prepare(
    "INSERT INTO queries (name, email, subject, message)
     VALUES (:name, :email, :subject, :message)"
);

$stmt->execute([
    ':name' => $data['name'],
    ':email' => $data['email'],
    ':subject' => $data['subject'] ?? null,
    ':message' => $data['message']
]);

echo json_encode([
    'success' => true,
    'message' => 'Query submitted'
]);
