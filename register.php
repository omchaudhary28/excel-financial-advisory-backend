<?php
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (
    !$data ||
    empty($data["name"]) ||
    empty($data["email"]) ||
    empty($data["password"])
) {
    echo json_encode([
        "success" => false,
        "message" => "All fields required"
    ]);
    exit;
}

$name = trim($data["name"]);
$email = trim($data["email"]);
$password = password_hash($data["password"], PASSWORD_DEFAULT);

$check = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$check->execute(["email" => $email]);

if ($check->fetch()) {
    echo json_encode([
        "success" => false,
        "message" => "Email already exists"
    ]);
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password, role)
     VALUES (:name, :email, :password, 'user')"
);

$stmt->execute([
    "name" => $name,
    "email" => $email,
    "password" => $password
]);

echo json_encode([
    "success" => true,
    "message" => "Registration successful"
]);
