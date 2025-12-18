<?php
require_once __DIR__ . "/cors.php";
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([":email" => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $user['email'],
        "role" => $user['role']
    ]
]);
