<?php
require_once __DIR__ . "/db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data["email"]) || empty($data["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing credentials"
    ]);
    exit;
}

$email = trim($data["email"]);
$password = $data["password"];

$stmt = $pdo->prepare(
    "SELECT id, name, email, password, role 
     FROM users 
     WHERE email = :email"
);
$stmt->execute(["email" => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password"])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid email or password"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"],
        "role" => $user["role"]
    ]
]);
