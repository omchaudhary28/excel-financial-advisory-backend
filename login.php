<?php
require_once 'db.php';
require_once 'jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id, name, email, password, role FROM users WHERE email = :email"
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

$token = JWT::generate([
    "id" => $user['id'],
    "email" => $user['email'],
    "role" => $user['role']
]);

echo json_encode([
    "success" => true,
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "role" => $user['role']
    ]
]);
