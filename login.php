<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/jwt_utils.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
    exit;
}

$token = generateJWT([
    "id" => $user['id'],
    "email" => $user['email']
]);

echo json_encode([
    "success" => true,
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "email" => $user['email']
    ]
]);
