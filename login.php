<?php
require_once 'db.php';
require_once 'jwt_utils.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Email and password required'
    ]);
    exit;
}

$email = trim($data['email']);
$password = $data['password'];

$stmt = $pdo->prepare(
    "SELECT id, name, email, password, role
     FROM users
     WHERE email = :email
     LIMIT 1"
);

$stmt->execute([':email' => $email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

/**
 * ✅ IMPORTANT FIX:
 * Explicitly check that $user is an array
 */
if (!is_array($user)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid credentials'
    ]);
    exit;
}

if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid credentials'
    ]);
    exit;
}

$token = JWT::generate([
    'id' => $user['id'],
    'email' => $user['email'],
    'role' => $user['role']
]);

echo json_encode([
    'success' => true,
    'token' => $token,
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role']
    ]
]);
