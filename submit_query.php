<?php
require_once 'db.php';

if (\['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

\ = json_decode(file_get_contents('php://input'), true);

if (!\) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

foreach (['name','email','message'] as \) {
    if (empty(\[\])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => "\ is required"]);
        exit;
    }
}

\ = \->prepare(
    "INSERT INTO queries (name, email, subject, message) VALUES (:name, :email, :subject, :message)"
);

\->execute([
    ':name' => \['name'],
    ':email' => \['email'],
    ':subject' => \['subject'] ?? null,
    ':message' => \['message']
]);

echo json_encode(['success' => true, 'message' => 'Query submitted']);
