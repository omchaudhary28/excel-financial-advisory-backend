<?php

require_once 'db_connect.php';
require_once 'jwt_utils.php';

header("Content-Type: application/json; charset=UTF-8");

// Read URL-encoded POST data
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

try {
    // 🔐 Fetch user (PostgreSQL PDO)
    $stmt = $pdo->prepare(
        "SELECT id, name, email, password, role
         FROM users
         WHERE email = :email
         LIMIT 1"
    );

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid credentials"
        ]);
        exit;
    }

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

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Server error"
    ]);
}
