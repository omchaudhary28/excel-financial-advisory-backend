<?php

require_once 'db_connect.php';
require_once 'jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");

// ✅ Read URL-encoded POST data (NO JSON → NO PREFLIGHT)
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

// 🔐 Fetch user securely
$stmt = $conn->prepare(
    "SELECT id, name, email, password, role FROM users WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

$user = $result->fetch_assoc();

// 🔑 Verify password
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Invalid credentials"
    ]);
    exit;
}

// 🎟️ Generate JWT
$token = JWT::generate([
    "id"    => $user['id'],
    "email" => $user['email'],
    "role"  => $user['role']
]);

echo json_encode([
    "success" => true,
    "token"   => $token,
    "user"    => [
        "id"    => $user['id'],
        "name"  => $user['name'],
        "email" => $user['email'],
        "role"  => $user['role']
    ]
]);

$stmt->close();
$conn->close();
