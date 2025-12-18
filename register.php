<?php
require_once 'cors.php';

require_once 'db_connect.php'; // PDO connection
require_once 'jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

$name     = trim($data['name'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['confirm_password'] ?? '';
$phone    = trim($data['phone'] ?? '');

if (!$name || !$email || !$password || !$confirm) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "All required fields must be filled"
    ]);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Passwords do not match"
    ]);
    exit;
}

// Check if email already exists
$stmt = $pdo->prepare(
    "SELECT id FROM users WHERE email = :email"
);
$stmt->execute(['email' => $email]);

if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode([
        "success" => false,
        "message" => "Email already registered"
    ]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, phone, role)
     VALUES (:name, :email, :password, :phone, 'user')"
);

$stmt->execute([
    'name'     => $name,
    'email'    => $email,
    'password' => $hashedPassword,
    'phone'    => $phone ?: null
]);

$userId = $pdo->lastInsertId();

// Generate JWT
$token = JWT::generate([
    "id"    => $userId,
    "email" => $email,
    "role"  => "user"
]);

echo json_encode([
    "success" => true,
    "message" => "Registration successful",
    "token"   => $token,
    "user" => [
        "id"    => $userId,
        "name"  => $name,
        "email" => $email,
        "role"  => "user"
    ]
]);
