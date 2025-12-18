<?php header('Content-Type: application/json'); ?>
<?php require_once __DIR__.'/cors.php'; ?>
<?php
require "db.php";

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if (!$name || !$email || !$password) {
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
$stmt->execute([$name, $email, $hash]);

echo json_encode(["success" => true, "message" => "Registered successfully"]);


