<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$email || !$password) {
    echo json_encode(["success" => false, "message" => "Missing credentials"]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    echo json_encode(["success" => false, "message" => "Invalid email or password"]);
    exit;
}

$token = bin2hex(random_bytes(32));

$pdo->prepare("UPDATE users SET token=? WHERE id=?")
    ->execute([$token, $user['id']]);

echo json_encode([
    "success" => true,
    "token" => $token,
    "user" => [
        "id" => $user['id'],
        "name" => $user['name'],
        "email" => $email
    ]
]);
